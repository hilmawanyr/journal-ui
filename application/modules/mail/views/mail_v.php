<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-taginput.css') ?>">

<section class="content">
  <div class="row">

    <div class="col-md-8 col-xs-12">
      <?php if ($this->session->flashdata('message_sent')) : ?>
        <div class="alert alert-dismissible alert-success">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Well done!</strong> <?= $this->session->flashdata('message_sent'); ?>
        </div>
      <?php endif; ?>
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-envelope"></i> Create E-mail</h3>
        </div>
        <div class="box-body">
          <form action="<?= base_url('send-mail') ?>" method="post" enctype="multipart/form-data">
            <div class="card-body">
              <div class="form-group row">
                <label for="recipient" class="col-sm-2 col-form-label">To</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="recipient" data-role="tagsinput" id="recipient" required="">
                </div>
              </div>
              <div class="form-group row">
                <label for="cc" class="col-sm-2 col-form-label" data-role="tagsinput">CC</label>
                <div class="col-sm-10">
                  <input type="email" class="form-control" name="cc" data-role="tagsinput" id="cc">
                </div>
              </div>
              <div class="form-group row">
                <label for="subject" class="col-sm-2 col-form-label">Subject</label>
                <div class="col-sm-10">
                  <input type="text" name="subject" class="form-control" id="subject" required="" />
                </div>
              </div>
              <textarea id="summernote" name="message" required=""></textarea>
              <button class="btn btn-warning pull-right"><i class="fa fa-paper-plane"></i> Send E-mail</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-xs-12">
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-paper-plane"></i> Sent E-mail List</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          The body of the box
        </div>
      </div>
    </div>

    <div class="col-md-4 col-xs-12">
      <?php if ($this->session->flashdata('success_template')) : ?>
        <div class="alert alert-dismissible alert-success">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>Well done!</strong> <?= $this->session->flashdata('success_template'); ?>
        </div>
      <?php endif; ?>
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bars"></i> Template List</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <a href="#template-modal" data-toggle="modal"><i class="fa fa-plus"></i> Create new template</a>
          <br><br>
          <span id="template-list"></span>
          <a href="#template-modal" data-toggle="modal">See all template</a>
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
      <form action="<?= base_url('save-template') ?>" method="post">
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

<script type="text/javascript" src="<?= base_url('assets/js/bootstrap-taginput.js') ?>"></script>
<script type="text/javascript">

  function pinToMail(key) {
    $.get('<?= base_url('get-template/') ?>' + key, function(res) {
      $('#summernote').summernote('code', res);
    });
  }

  $('#recipient').tagsinput('items');

  $(document).ready(function() {
    // summernote plugin
    $('#summernote').summernote({
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

    // load template list
    $.get('<?= base_url('template-list') ?>', function(res) {
      var data = JSON.parse(res);
      data.forEach(function(datas) {
        $('#template-list').append(`
          <div class="row">
            <div class="col-md-10 col-xs-2">
              <span data-toggle="tooltip" title="${datas.template}">
                ${datas.template.substring(0, 35)}...
              </span>
            </div>
            <div class="col-md-2 col-xs-2" style="text-align: center;">
              <i
                class="fa fa-thumb-tack"
                style="cursor: pointer"
                data-toggle="tooltip"
                onclick="pinToMail('${datas.key}')"
                title="use template">
              </i>
            </div>
          </div>
          <hr>
        `);
      })
    })
  });
</script>
