<div class="modal fade bd-example-modal-sm" id="delete_modal" tabindex="-1" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="exampleModalLabel2">Delete Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close">
                </button>
            </div>
            <form id="delete_form">
                @csrf
                <input type="hidden" name="id" id="deleteRecordId">
                <div class="modal-body">
                    <strong class="para3"> Are you sure you want to Delete this record?</strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="deleteRecordBtn" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
