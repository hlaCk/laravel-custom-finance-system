<template>
  <!-- Modal -->
  <div
    class="fixed pin-l pin-t p-8 h-full w-full z-50"
    :class="{ hidden: !open, flex: open }"
  >
    <!-- Modal Background -->
    <div
      class="absolute bg-black opacity-75 pin-l pin-t h-full w-full"
      @click="close"
    ></div>

    <!-- Modal Content -->
    <div class="flex flex-col bg-white p-4 h-full relative w-full">
      <!-- Header bar -->
      <div class="border-b border-40 pb-4 mb-4">
        <div class="flex -mx-4">
          <!-- Heading -->
          <div class="px-4 self-center">
            <h3>{{ __("Existing Media") }}</h3>
          </div>

          <!-- Search -->
          <div class="px-4 self-center">
            <div class="relative">
              <icon
                type="search"
                class="absolute search-icon-center ml-3 text-70"
              />
              <input
                type="search"
                v-bind:placeholder="__('Search by name or file name')"
                class="pl-search form-control form-input form-input-bordered w-full"
                v-model="requestParams.search_text"
                @input="search"
                @change="search"
                @keydown.enter.prevent="search"
              />
            </div>
          </div>

          <!-- Close -->
          <div class="px-4 ml-auto self-center">
            <button
              type="button"
              class="form-file-btn btn btn-default btn-primary"
              @click="close"
            >
              {{ __("Close") }}
            </button>
          </div>
        </div>
      </div>

      <div class="flex-grow overflow-x-hidden overflow-y-scroll">
        <!-- When we have results show them -->

        <div
          class="flex flex-wrap -mx-4 -mb-8"
          v-if="response.data.data.length > 0"
        >
          <template v-for="(item, key) in medias">
            <existing-media-item
              :item="item"
              @refreshData="fireRequest"
              :key="key"
              :thumbnail="getThumbnail(item)"
              @select="$emit('select', item) && close()"
            ></existing-media-item>
          </template>
        </div>

        <!-- Show "Loading" or "No Results Found" text -->
        <h4 class="text-center m-8" v-if="loading">{{ __("Loading...") }}</h4>
        <h4 class="text-center m-8" v-else-if="items.length == 0">
          {{ __("No results found") }}
        </h4>
      </div>
      <nav class="flex items-center">
        <div class="flex text-sm">
          <button
            rel="first"
            dusk="first"
            @click.prevent="selectedPage(1)"
            :class="
              currentPage == 1 ? 'text-80 opacity-50' : 'text-primary dim'
            "
            :disabled="isDisabled"
            class="font-mono btn btn-link h-9 min-w-9 px-2 border-r border-50"
          >
            «
          </button>
          <button
            @click.prevent="prevPage"
            rel="prev"
            :class="
              currentPage == 1 ? 'text-80 opacity-50' : 'text-primary dim'
            "
            :disabled="isDisabled"
            dusk="previous"
            class="font-mono btn btn-link h-9 min-w-9 px-2 border-r border-50"
          >
            ‹
          </button>
          <button
            v-for="(page, index) in pages"
            :key="index"
            dusk="page:1"
            @click.prevent="selectedPage(page)"
            :class="
              currentPage == page ? 'text-80 opacity-50' : 'text-primary dim'
            "
            :disabled="currentPage == page ? true : false"
            class="btn btn-link h-9 min-w-9 px-2 border-r border-50 "
          >
            {{ page }}
          </button>
          <button
            @click.prevent="nextPage"
            :class="
              lastPage < currentPage ? 'text-80 opacity-50' : 'text-primary dim'
            "
            :disabled="isDisabledLast"
            class="font-mono btn btn-link h-9 min-w-9 px-2 border-r border-50"
          >
            ›
          </button>
          <button
            :class="
              lastPage < currentPage ? 'text-80 opacity-50' : 'text-primary dim'
            "
            @click.prevent="selectedPage(lastPage)"
            class="font-mono btn btn-link h-9 min-w-9 px-2 border-r border-50"
            :disabled="isDisabledLast"
          >
            »
          </button>
        </div>
      </nav>
    </div>
  </div>
</template>

<script>
import ExistingMediaItem from "./ExistingMediaItem";

export default {
  components: {
    ExistingMediaItem
  },
  data() {
    let aThis = this;
    return {
      thumbnails: {},
      requestParams: {
        search_text: "",
        page: 1,
        per_page: ""
      },
      items: [],
      response: {
        data: {data: []},
        meta: {
          last_page: 1,
          current_page: 1,
        }
      },
      loading: false,
      search: _.debounce(function () {
        aThis.refresh();
      }, 750)
    };
  },
  mounted() {
    this.configRequest();
  },
  props: {
    open: {
      default: false,
      type: Boolean
    }
  },
  computed: {
    medias() {
      return this.response.data && this.response.data.data || [];
    },
    isDisabled() {
      return this.currentPage == 1 ? true : false;
    },
    isDisabledLast() {
      return this.currentPage == this.lastPage
        ? true
        : false;
    },
    currentPage() {
      return this.requestParams.page;
    },
    lastPage() {
      return this.response.data && this.response.data.meta && this.response.data.meta.last_page || 1;
    },
    showNextPage() {
      if (
        this.items.length ==
        this.requestParams.page * this.requestParams.per_page
      ) {
        return true;
      }
      return false;
    },

    metaCurrentPage() {
      return this.response.data && this.response.data.meta && this.response.data.meta.current_page || 1;
    },

    pages() {
      let startIndex = this.metaCurrentPage - 2;
      if (startIndex < 1) {
        startIndex = 1;
      }
      let endIndex = startIndex + 4;

      if (endIndex > this.lastPage) {
        endIndex = this.lastPage;
        startIndex = this.lastPage - 4;
        startIndex = startIndex < 1 ? 1 : startIndex;
      }

      return Array.from(
        {length: endIndex - startIndex + 1},
        (_, i) => i + startIndex
      );
    }
  },
  methods: {
    getThumbnail(item) {
      return this.thumbnails[this.getExtension(item)] ||
        ('__media_urls__' in item && 'indexView' in item.__media_urls__ && item.__media_urls__.indexView) ||
        item.original_url;
    },
    getExtension(item) {
      return item && item.file_name && item.file_name.substring(item.file_name.lastIndexOf(".") + 1);
    },
    close() {
      this.$emit("close");
    },
    selectedPage(page) {
      this.requestParams.page = page;
      this.fireRequest();
    },
    refresh() {
      this.requestParams.page = 1;
      return this.fireRequest().then(response => {
        this.items = response.data.data;
        return response;
      });
    },
    nextPage() {
      this.requestParams.page += 1;
      return this.fireRequest().then(response => {
        this.items = this.items.concat(response.data.data);
        return response;
      });
    },
    prevPage() {
      this.requestParams.page -= 1;
      return this.fireRequest().then(response => {
        this.items = this.items.concat(response.data.data);
        return response;
      });
    },
    fireRequest() {
      // Set loading to true
      this.loading = true;

      return this.createRequest()
        .then(response => {
          this.response = response;
          return response;
        })
        .finally(() => {
          // Set loading to false
          this.loading = false;
        });
    },
    /**
     * Request builders the request
     */
    createRequest() {
      return Nova.request().get(
        `/nova-vendor/ebess/advanced-nova-media-library/media`,
        {
          params: this.requestParams
        }
      );
    },
    /**
     * Request builders the request
     */
    configRequest() {
      // Set loading to true
      this.loading = true;

      return Nova.request().get(`/nova-vendor/ebess/advanced-nova-media-library/config`)
        .then(response => {
          this.requestParams.per_page = response.data.per_page || this.requestParams.per_page;
          this.thumbnails = response.data.thumbnails || {};
          return response;
        })
        .finally(() => {
          // Set loading to false
          this.loading = false;
        });
    }
  },
  watch: {
    open: function (newValue) {
      if (newValue) {
        if (this.items.length == 0) {
          this.refresh();
        }
        document.body.classList.add("overflow-x-hidden");
        document.body.classList.add("overflow-y-hidden");
      } else {
        document.body.classList.remove("overflow-x-hidden");
        document.body.classList.remove("overflow-y-hidden");
      }
    }
  }
};
</script>
