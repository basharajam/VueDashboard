  <!-- Update Item Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Update Component</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form class="SubmitSection" data-comp="">
                
                <div class="mb-3">
                    <input type="text" id='title' placeholder="Section Title" name="SectionTitleI"  class="form-control"  >
                </div>
                <div class="mb-3">
                    <input type="text" id="value" placeholder="Value" name="SectionValI" class="form-control" >
                </div>
                <div class="mb-3">
                    <select name="ProdByTaxType" id="type" name="SectionTypeI" class="form-control" disabled>
                        <option value="tag"  >Tag</option>
                        <option value="category"  >Category</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="number" id="ItemNum" placeholder="Items number" name="ItemNumI" class="form-control" >
                </div>
                <div class="mb-3">
                    <input type="text" id="TransKey" name="TransKeyI" placeholder="Item Translation Key" class="form-control">
                </div>
                <div class="mb-3">
                    <input type="text" id="link" placeholder="Link" name="linkI" class="form-control" >
                </div>
                <div class="mb-3">
                    <select name="displayMobile" id="displayMobile" class="form-control">
                        <option >Select Component Display mode in mobile</option>
                        <option value="grid"  >Grid 2 Items In Row</option>
                        <option value="grid3" >Grid 3 Items In Row</option>
                        <option value="slider" >slider</option>
                        <option value="full">Full</option>
                        <option value="hide">Hide</option>
                    </select>
                </div>
                <div class="mb-3">
                    <select name="displayDesktop" id="displayDesktop" class="form-control">
                        <option >Select Component Display mode in Desktop</option>
                        <option value="list" >List</option>
                        <option value="slider">slider</option>
                        <option value="full">Full</option>
                        <option value="hide">Hide</option>
                    </select>
                </div>
                
                <input type="hidden" name="compId" id='compId'>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Update Item Modal End -->

    <!-- Add New Component Modal  -->
    <div class="modal fade" id="AddCompModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Add New Component</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="SaveCompSection" method="POST" action="{{ route('SaveComp') }}">
                    
                    <div class="mb-3">
                        <select id="type" name="CompTypeNI" class="form-control">
                            <option value="ProdList"  >ProdList</option>
                            <option value="banner">Banner</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select id="type" name="SectionTypeNI" class="form-control">
                            <option value="tag"  >Tag</option>
                            <option value="category">Category</option>
                            <option value="link"  >link</option>
                            <option value="offers">offers</option>
                            <option value="newest">newest</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="text"  placeholder="Translation Key" name="TransKeyNI"  class="form-control"  >
                    </div>
                    <div class="mb-3">
                        <input type="text"  placeholder="Section Title" name="SectionTitleNI"  class="form-control"  >
                    </div>
                    <div class="mb-3">
                        <input type="text" placeholder="Value" name="SectionValNI" class="form-control" >
                    </div>
                    <div class="mb-3">
                        <input type="number"  placeholder="Items number" name="ItemNumNI" class="form-control" >
                    </div>
                    <div class="mb-3">
                        <input type="text" placeholder="Link" name="linkNI" class="form-control" >
                    </div>
                    <div class="mb-3">
                        <select name="displayMobileNI"  class="form-control">
                            <option >Select Component Display mode in mobile</option>
                            <option value="grid"  >Grid 2 Items In Row</option>
                            <option value="grid3" >Grid 3 Items In Row</option>
                            <option value="slider" >slider</option>
                            <option value="full">Full</option>
                            <option value="hide">Hide</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select name="displayDesktopNI"  class="form-control">
                            <option >Select Component Display mode in Desktop</option>
                            <option value="list" >List</option>
                            <option value="slider">slider</option>
                            <option value="full">Full</option>
                            <option value="hide">Hide</option>
                        </select>
                    </div>
                    <input type="hidden" name="compwhereNI" id='compId'>
                    {{ csrf_field() }}
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
          </div>
        </div>
      </div>
      <!-- End Add New Component Modal -->

    <!-- Del Component Modal Start -->
    <div class="modal fade" id="DelCompModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Delete Component</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="DelCompSection" method="POST" action="{{ route('DelComp') }}">
                    <input type="text" class="form-control" name="CompDelI">
                    <input type="hidden" name="CompIdI">
                    {{ csrf_field() }}
                    <div class="mb-3">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
  <!-- End Del compoenent Modal -->
    
      