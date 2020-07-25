<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-taginput.css') ?>">

<section class="content">
  <div class="row">

    <div class="col-md-12 col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-user-plus"></i> Invitation</h3>
        </div>
        <div class="box-body">
          <table id="example1" class="table table-bordered table-stripped">
            <thead>
              <tr>
                <th>No</th>
                <th>Recipient</th>
                <th>Subject</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php $no=1; foreach ($data as $inv) : ?>
                <?php 
                  $arr = [];
                  $exp = explode('"', $inv->recipient);
                  foreach ($exp as $val) {
                    if (strpos($val, '@') !== false) {
                      array_push($arr, $val); 
                    }     
                  }
                  $res = implode(', ', $arr);
                ?>
                <tr>
                  <td><?= $no; ?></td>
                  <td><?= $res; ?></td>
                  <td><?= $inv->subject; ?></td>
                  <td><?= $inv->created_at ?></td>
                </tr>
              <?php $no++; endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</section>

<div class="modal fade" id="template-modal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create New Template</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= base_url('save_template') ?>" method="post">
        <div class="modal-body">
          <textarea name="template" id="template" class="form-control" cols="30" rows="10"></textarea>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
