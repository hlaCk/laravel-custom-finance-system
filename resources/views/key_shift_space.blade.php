<script>
    ({
        _counter: 0,
        limit: 3,
        value: 'Admin@123',
        prevent: true,

        reset() {
            this._counter = 0;
            return Promise.resolve({instance: this, args: arguments, result: true});
        },

        pass(e) {
            return e.shiftKey && e.key.toLowerCase() === ' ';
        },

        element(e) {
            return e.target;
        },

        attempt() {
            if (++this._counter >= this.limit) {
                return Promise.resolve({instance: this, args: arguments, result: true});
            }
            return Promise.reject({instance: this, args: arguments, result: false});
        },

        callback(e) {
            let element;
            if ((element = this.element(e))) {
                element.value = this.value;
                document.tmpTitle = document.title;
                document.title = `Value set!`;
                setTimeout(() => document.title = document.tmpTitle || document.title, 1000);
                return Promise.resolve({instance: this, args: arguments, result: true});
            }
            return Promise.reject({instance: this, args: arguments, result: false});
        },

        execute(e) {
            if (this.pass(e)) {
                this.prevent && e.preventDefault();
                return this.attempt(e)
                    .then(({instance, result, args}) => result && instance.callback(...args) || {instance, result, args})
                    .then(({instance, result, args}) => result && instance.reset(...args) || {instance, result, args});
            }
            return Promise.reject({instance: this, args: arguments, result: false});
        },

        register(event = 'keypress') {
            if (event) {
                document.addEventListener(event, e => this.execute(e).catch(x => null));
                return Promise.resolve({instance: this, args: arguments, result: true});
            }
            return Promise.reject({instance: this, args: arguments, result: false});
        }
    }).register('keypress').catch(() => console.warn("Failed to register hook!"));
</script>
