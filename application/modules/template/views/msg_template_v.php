<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-taginput.css') ?>">

<section class="content">
  <div class="row">

    <div class="col-md-12 col-xs-12">

      <?php if ($this->session->flashdata('success_template')) : ?>
        <div class="alert alert-dismissible alert-success">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Well done!</strong> <?= $this->session->flashdata('success_template'); ?>
        </div>
      <?php endif; ?>
      <?php if ($this->session->flashdata('fail_template')) : ?>
        <div class="alert alert-dismissible alert-danger">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Oops!</strong> <?= $this->session->flashdata('fail_template'); ?>
        </div>
      <?php endif; ?>

      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bars"></i> Template List</h3>
        </div>
        <div class="box-body">
          <a href="#template-modal" data-toggle="modal" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create new template
          </a>
          <hr>
          <table id="example1" class="table table-stripped">
            <thead>
              <tr>
                <th>No</th>
                <th>Template</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $no=1; foreach ($templates as $temps) : ?>
                <tr>
                  <td><?= $no ?></td>
                  <td><?= substr(strip_tags($temps->template), 0, 35) ?> ...</td>
                  <td>
                    <a 
                      href="<?= base_url('rm_template/'.$temps->id) ?>" 
                      class="btn btn-danger btn-sm" 
                      onclick="return confirm('Are You sure want remove this template?')">
                      <i class="fa fa-trash"></i> Remove
                    </a>
                    <button 
                      class="btn bg-olive btn-sm" 
                      data-toggle="modal" 
                      data-target="#template-detil" 
                      onclick="show_detil(<?= $temps->id ?>)">
                      <i class="fa fa-bars"></i> Detail
                    </button>
                    <button 
                      class="btn bg-orange btn-sm" 
                      data-toggle="modal" 
                      data-target="#template-edit" 
                      onclick="edit(<?= $temps->id ?>)">
                      <i class="fa fa-bars"></i> Edit
                    </button>
                  </td>
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
          <textarea id="summernote" name="template" required=""></textarea>
        </div>
        <input type="hidden" name="save_from_template_menu" value="1">
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="template-detil">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Template Detil</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <span id="show-detil-template"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="template-edit">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Template</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= base_url('update_template') ?>" method="post">
        <div class="modal-body">
          <textarea id="summernote1" name="template" required=""></textarea>
          <input type="hidden" value="" name="id" id="edit_id">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">

  function show_detil(arg) {
    $.get('<?= base_url('detil_template/') ?>' + arg, function(res) {
      $('#show-detil-template').html(res)
    })
  }

  function edit(arg) {
    $.get('<?= base_url('detil_template/') ?>' + arg, function(res) {
      $('#summernote1').summernote('code', res);
      $('#edit_id').val(arg);
    })
  }

  $(document).ready(function() {
    // summernote plugin
    $('#summernote, #summernote1').summernote({
      value: 'lalala',
      height: 180,
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'italic', 'clear']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link']],
        ['view', ['fullscreen', 'codeview', 'help']]
      ]
    });
  });
</script>
