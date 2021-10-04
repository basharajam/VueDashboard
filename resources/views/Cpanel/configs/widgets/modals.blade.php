<!-- Modal -->
<div class="modal fade" id="AddConfigBtn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add Config Modal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form class="SubmitSection" data-comp="">
                <div class="mb-3">
                    <input type="text" id='title' placeholder="Config Name " name="ConfigNameI"  class="form-control"  >
                </div>
                <div class="mb-3">
                    <input type="text" id="value" placeholder="Config Key" name="ConfigKeyI" class="form-control" >
                </div>
                <div class="mb-3">
                    <input type="text" id="value" placeholder="Config Value" name="ConfigValueI" class="form-control" >
                </div>
                <div class="mb-3">
                  <input type="text" id="value" placeholder="Config Sub Value" name="ConfigSubValueI" class="form-control" >
                  <input type="hidden" name="ConfigTypeI">
                </div>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary SaveConfigBtn">Save changes</button>
        </div>
      </div>
    </div>
  </div>