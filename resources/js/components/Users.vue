<template>
    <div class="container">
      <div class="row mt-5">
         <div class="col-md-12">
           <div class="card">
             <div class="card-header">
               <h3 class="card-title">Users Table</h3>


               <div class="card-tools">
                  <button class="btn btn-outline-success" @click="newModal">
                     Add new user
                     <i class="fas fa-user-plus fa-fw"></i>
                  </button>
               </div>
             </div>
             <!-- /.card-header -->
             <div class="card-body table-responsive p-0">
               <table class="table table-hover">
                 <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Type</th>
                      <th>Registered At</th>
                      <th>Action</th>
                    </tr>
                 </thead>
                 <tbody>
                   <tr v-for="user in users" :key="user.id">
                      <td>{{user.id}}</td>
                      <td>{{user.name}}</td>
                      <td>{{user.email}}</td>
                      <td>{{user.type | upText}}</td>
                      <td>{{user.created_at | myDate}}</td>
                      <td>
                         <button @click="editModal(user)" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit blue"></i>
                         </button>
                         <button @click="deleteUser(user.id)" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash red"></i>
                         </button>
                      </td>
                    </tr>
                 </tbody>
               </table>
             </div>
             <!-- /.card-body -->
           </div>
           <!-- /.card -->
         </div>
      </div>
      <!-- Modal -->
      <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 v-show="!editmode" class="modal-title" id="addNewLabel">New user</h5>
              <h5 v-show="editmode" class="modal-title" id="addNewLabel">Edit user</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form @submit.prevent="editmode ? updateUser() : createUser()" @keydown="form.onKeydown($event)">
               <div class="modal-body">
                  <div class="form-group">
                     <input v-model="form.name" type="text" name="name"
                     placeholder="Name"
                     class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                     <has-error :form="form" field="name"></has-error>
                  </div>
                  <div class="form-group">
                     <input v-model="form.email" type="email" name="email"
                     placeholder="Email Address"
                     class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                     <has-error :form="form" field="email"></has-error>
                  </div>
                  <div class="form-group">
                     <textarea v-model="form.bio" name="bio" id="bio"
                     placeholder="Short bio for user (Optional)"
                     class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                     <has-error :form="form" field="bio"></has-error>
                  </div>
                  <div class="form-group">
                     <select name="type" v-model="form.type" id="type" class="form-control"
                     :class="{ 'is-invalid': form.errors.has('type') }">
                        <option value="">Select user role</option>
                        <option value="admin">Admin</option>
                        <option value="user">Standard user</option>
                        <option value="author">Author</option>
                     </select>
                     <has-error :form="form" field="type"></has-error>
                  </div>
                  <div class="form-group">
                     <input v-model="form.password" type="password" name="password" id="password"
                     class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                     <has-error :form="form" field="password"></has-error>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                  <button v-show="editmode" type="submit" class="btn btn-outline-success">Update</button>
                  <button v-show="!editmode" type="submit" class="btn btn-outline-success">Create</button>
               </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
    export default {
      data(){
         return{
           editmode : false,
           users : {},
            form: new Form({
               id:'',
               name: '',
               email: '',
               password: '',
               type: '',
               bio: '',
               photo:''
            })
         }
      },
      methods:{
        updateUser(){
          this.$Progress.start();
          // console.log('editing data');
          this.form.put('api/user/'+this.form.id)
          .then(() => {
            //success
            $('#addNew').modal('hide');
            toast({
              type: 'success',
              title: 'User updated successfully'
            })
            Fire.$emit('AfterCreate');
            this.$Progress.finish();
          })
          .catch(() => {
            this.$Progress.fail();
            swal("Failed", "There was something wrong", "warning");
          })
        },

        editModal(user){
          this.editmode = true;
          this.form.reset();
          $('#addNew').modal('show');
          this.form.fill(user);
        },

        newModal(){
          this.editmode = false;
          this.form.reset();
          $('#addNew').modal('show');
        },

        deleteUser(id){
          swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete user'
          }).then((result) => {
            //send request to server
            if (result.value) {
              this.form.delete('api/user/'+id).then(()=>{
                swal(
                  'Deleted!',
                  'User has been deleted.',
                  'success'
                )
                Fire.$emit('AfterCreate');
              }).catch(() => {
                this.$Progress.fail();
                swal("Failed", "There was something wrong", "warning");
              });
            }
          })
        },

        loadUsers(){
          axios.get('api/user').then(({data}) => (this.users = data.data))
        },

        createUser(){
          //submit the form via a POST request
          this.$Progress.start();
          this.form.post('api/user')
          .then(() => {
            Fire.$emit('AfterCreate');
            $('#addNew').modal('hide');
            toast({
              type: 'success',
              title: 'User created successfully'
            })
            this.$Progress.finish();
          })
          .catch(() => {
            this.$Progress.fail();
          });
        }
      },
      created() {
         this.loadUsers();
         Fire.$on('AfterCreate',() => {
           this.loadUsers();
         });
         // setInterval(() => this.loadUsers(), 3000);

      }
    }
</script>
