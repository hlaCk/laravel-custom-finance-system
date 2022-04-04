<template>
  <div class="border-40 group px-4 pb-4 mb-4 w-seven">
    <div class="shadow">
      <div class="overflow-hidden relative w-full" style="padding-top:100%;">
        <img
          v-if="thumbnail || '__media_urls__' in item && 'indexView' in item.__media_urls__"
          :src="thumbnail || item.__media_urls__.indexView"
          class="absolute block h-full pin-t pin-l w-full"
          style="object-fit: cover"
        />
        <button
          type="button"
          class="absolute form-file-btn btn pt-0.5 btn-primary btn-xs pin-t pin-r m-2"
          @click="$emit('select')"
        >
          {{ __("Select") }}
        </button>
        <button
          v-if="'__media_urls__' in item && 'indexView' in item.__media_urls__"
          type="button"
          class="absolute form-file-btn btn-xs btn  btn-primary pin-t pin-l m-2"
          @click.prevent="deleteMedia(item.id)"
        >
          <svg style="width: 17px;padding-top: 2px;height: 20px;" viewBox="0 0 24 24">
            <path
              fill="currentColor"
              d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"
            />
          </svg>
        </button>
      </div>
      <div ref="file_name_dev" class="p-3 border-l border-r border-b border-40" @dblclick="showRename">
        <h4 class="truncate h-4 mb-1" v-if="'name' in item && !isRenaming">
          {{ item.name }}
        </h4>
        <h5 class="truncate text-80" v-if="'file_name' in item && !isRenaming">
          {{ item.file_name }}
        </h5>
        <form
          id="fileNameForm"
          class="form-inline"
          @submit.prevent="rename(item)"
        >
          <div class="form-group">
            <input
              type="text"
              class="form-control form-control-plaintext form-input-bordered w-full"
              id="fileName"
              v-model="unsavedName"
              :placeholder="__('Enter file name')"
              v-if="isRenaming"
              @blur="hideRename"
            />
          </div>
        </form>
      </div>

    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    item: {
      default: function () {
        return {};
      },
      type: Object
    },
    thumbnail: {
      default: function () {
        return this.item.original_url;
      },
      type: String
    }
  },
  data() {
    return {
      isRenaming: false,
      unsavedName: undefined,
    }
  },
  mounted() {
    this.unsavedName = this.item.name;
  },
  methods: {
    getExtension() {
      return this.item && this.item.file_name && this.item.file_name.substring(this.item.file_name.lastIndexOf(".") + 1);
    },
    hideRename($event) {
      this.isRenaming = false;
    },
    showRename($event) {
      if (this.isRenaming || $event.target.tagName === 'INPUT') {
        return;
      }
      let parent = this.$refs.file_name_dev;
      this.isRenaming = true;
      setTimeout(() => {
        let fileNameInput = parent.querySelector('#fileName');
        if (fileNameInput) {
          fileNameInput.focus && fileNameInput.focus();
          fileNameInput.select && fileNameInput.select();
        }
      }, 10)
    },
    rename(item) {
      this.item.name = item.name = this.unsavedName;
      this.isRenaming = false;
      axios
        .post("/api/rename-file", item)
        .then(success => {
          this.$emit("refreshData", "");
          this.$toasted.show(this.__("File renamed successfully!"), {
            type: 'success',
            duration: 1000
          });
        })
        .catch(error => {
          this.$emit("refreshData", "");
          this.$toasted.show(this.__("File Not Exist!"), {
            type: 'error',
            duration: 1000
          });
        });
    },
    deleteMedia(id) {
      confirm(this.__("Are you sure?")) && axios.delete('/api/media-delete/' + id, "").then(succes => {
        this.$emit("refreshData", "");
        this.$toasted.show(this.__("File Deleted Successfully!"), {
          type: 'success',
          duration: 1000
        });
      }).catch(error => {
        this.$toasted.show(this.__("Error!"), {
          type: 'error',
          duration: 1000
        });
      })
    }
  }
};
</script>
<style scoped>
.form-inline {
  display: flex;
  justify-content: space-evenly;
}

.pin-c {
  left: 18%;
}

.w-seven {
  width: 10%;
}
</style>
