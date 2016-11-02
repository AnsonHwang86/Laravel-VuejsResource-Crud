<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta id="token" name="token" value="{{ csrf_token() }}">
    <title>Welcome to Vue Js Item CRUD</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.3/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/1.0.3/vue-resource.min.js"></script>
    <style type="text/css">
        .modal-mask {
            position: fixed;
            z-index: 9998;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, .5);
            display: table;
            transition: opacity .3s ease;
        }

        .modal-wrapper {
            display: table-cell;
            vertical-align: middle;
        }

        .modal-container {
            width: 800px;
            margin: 0px auto;
            padding: 20px 30px;
            background-color: #fff;
            border-radius: 2px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
            transition: all .3s ease;
            font-family: Helvetica, Arial, sans-serif;
        }

        .modal-header h3 {
            margin-top: 0;
            color: #42b983;
        }

        .modal-body {
            margin: 20px 0;
        }

        .modal-default-button {
            float: right;
        }

        /*
         * the following styles are auto-applied to elements with
         * v-transition="modal" when their visiblity is toggled
         * by Vue.js.
         *
         * You can easily play with the modal transition by editing
         * these styles.
         */

        .modal-enter, .modal-leave {
            opacity: 0;
        }

        .modal-enter .modal-container,
        .modal-leave .modal-container {
            -webkit-transform: scale(1.1);
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div id="manage-vue" class="container">

        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Laravel Vue Js Item CRUD</h2>
                </div>
                <div class="pull-right">
                    <button class="btn btn-success" type="button" data-toggle="modal" data-target="#create-item" @click="showCreateModal = true;">
                        Create Item
                    </button>
                </div>
            </div>
        </div>

        <!-- Item Listing -->
        <table class="table table-bordered">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th width="200px">Action</th>
            </tr>
            <tr v-for="item in pagination.data">
                <td>@{{ item.title }}</td>
                <td>@{{ item.description }}</td>
                <td>
                    <button class="btn btn-primary" @click.prevent="editItem(item)">Edit</button>
                    <button class="btn btn-danger" @click.prevent="deleteItem(item)">Delete</button>
                </td>
            </tr>

        </table>
        <!-- Pagination -->
        <nav aria-label="...">
            <ul class="pagination" :attr ="pagination.current_page">
                <li v-if="1 >= pagination.current_page" class="disabled">
                    <a href="#" aria-label="Previous"><span aria-hidden="true">&laquo</span></a>
                </li>
                <li v-else>
                    <a @click.prevent="getVueItems(pagination.current_page-1)" aria-label="Previous"><span aria-hidden="true">&laquo</span></a>
                </li>


                <template v-for="n in pagination.last_page">
                    <li v-bind:class="[ n == pagination.current_page ? 'active' : '']">
                        <span @click.prevent="getVueItems(n)">@{{ n }}</span>
                    </li>
                </template>

                <li class="disabled" v-if="pagination.current_page >= pagination.last_page">
                    <a href="#" aria-label="Next"><span aria-hidden="true">&raquo</span></a>
                </li>
                <li v-else>
                    <a aria-label="Next" @click.prevent="getVueItems(pagination.current_page+1)"><span aria-hidden="true">&raquo</span></a>
                </li>
                <li>
                    <pre>@{{ pagination }}</pre>
                </li>

            </ul>
        </nav>

        <modal :show = "showModal" @close="showModal=false" :fl-item = "fillItem" @ud-item="updateItem"></modal>
        <modal :show = "showCreateModal" @close="showCreateModal=false" :fl-item="fillItem">
            <span slot="header">Create Item</span>
            <span slot="input-id"></span>
            <button slot="action" type="submit" class="btn btn-primary" @click="createItem">Create</button>
        </modal>
    </div>
</body>

<script type="text/x-template" id="modal-template">
    <div class="modal-mask" v-show="show" translate="modal">
        <div class="modal-wrapper">
            <div class="modal-container">
                <div @click="$emit('close')">
                    X
                </div>
                <div class="modal-header">
                    <slot name="header">
                        Edit Item
                    </slot>
                </div>

                <div class="modal-body">
                    <form>
                        <div class="hidden">
                            <slot name="input-id">
                                <input type="text" id="id" :value="flItem.id">
                            </slot>
                        </div>
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <textarea type="text" id="title" class="form-control" v-model="flItem.title"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea type="text" id="description" class="form-control" v-model="flItem.description"></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <slot name="action">
                        <button type="submit" class="btn btn-primary" @click="updateItem">Submit</button>
                    </slot>

                </div>
            </div>
        </div>
</script>

<script>
Vue.http.headers.common['X-CSRF-TOKEN'] = document.getElementById('token').getAttribute('value')

Vue.component('modal', {
    template: '#modal-template',
    props: ['show', 'flItem'],
    methods: {
        updateItem () {
            var id = this.flItem.id
            this.$http.put('vueitems/' +id, this.flItem).then( (response) =>{
                this.$emit('close')
                this.$emit('ud-item', this.flItem)
            })
        }
    }
})

var vm = new Vue({
        el:'#manage-vue',
        data: {
            showModal: false,
            showCreateModal:false,
            tmpItem: null,
            fillItem: {
                title: null,
                id: 0,
                description: null
            },
            pagination: {
                total: 0,
                data:{
                    item:{
                        title: null,
                        description: null
                    }
                },
                current_page: 1,
                last_page: 0,
                from: 1,
                to: 0
            }
        },
    methods: {
            getVueItems (page) {
                this.$http.get('vueitems?page=' + page).then( (response) => {
                    Vue.set(this.$data, 'pagination', response.data)
                    this.current_page = page
//                    Vue.set(this.$data, 'items', response.data.data)
//                    this.pagination = response.data;
                })
            },
            createItem () {
                this.$http.post('vueitems', this.fillItem).then( (response) => {
                    this.showCreateModal = false
                    this.fillItem = {id:0, title: '', description: ''}
                    this.getVueItems(this.current_page)
                } )

            },
            deleteItem(item){
                this.$http.delete('vueitems/' + item.id).then( (response) => {
                    this.pagination.data.splice(this.pagination.data.indexOf(item),1);
                })
            },
            editItem(item){
                this.showModal = true
                this.fillItem = Object.assign({},item)
                this.tmpItem = item
//                console.log(this.tmpItem)
            },
            updateItem (flItem) {
                var index = this.pagination.data.indexOf(this.tmpItem)
                this.pagination.data.fill(flItem, index, index+1)
                this.fillItem = {id:0, title:null, description: null}
                this.tmpItem = null
            }
    },

    created () {
            this.getVueItems(this.pagination.current_page)
    }
})
</script>
</html>