<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mail extends CI_Controller{

  private $userid, $username;

  public function __construct()
  {
    parent::__construct();
    $this->userid = $this->session->userdata('login_sess')['userid'];
    $this->username = $this->session->userdata('login_sess')['name'];
  }

  public function _remap(string $method, array $param=[])
  {
    if (!$this->session->userdata('login_sess')) {
      redirect('redirect_auth/'.$method);
    }

    return method_exists($this, $method)
      ? call_user_func_array(array($this, $method), $param)
      : call_user_func_array([$this, "index"], [$method]);
  }

  public function index(string $mail='')
  {
    $data['pagename']  = 'Send e-mail';
    $data['recipient'] = ($mail !== 'index') ? base64_decode($mail) : '';
    $data['page'] = 'mail_v';
    $this->load->view('template/template', $data);
  }

  public function save_template()
  {
    $template = $this->input->post('template');
    $data = [
      '_key' => md5(date('YmdHis')),
      'template' => $template,
      'owner' => $this->userid
    ];
    $this->db->insert('message_template', $data);
    $this->session->set_flashdata('success_template', 'New message template saved!');

    if ($this->input->post('save_from_template_menu')) {
      redirect('msg_template');
    }
    redirect('mail');
  }

  public function detail_template($id)
  {
    $data['template'] = $this->db->get_where('message_template', ['id' => $id])->row();
    $this->load->view('template_modal_v', $data);
  }

  public function template_list()
  {
    $templates = $this->db->query("SELECT * FROM message_template WHERE deleted_at IS NULL")->result();
    foreach ($templates as $template) {
      $template_data[] = [
        'key' => $template->_key,
        'template' => strip_tags($template->template)
      ];
    }
    echo json_encode($template_data);
  }

  public function get_detail_template($key)
  {
    $template = $this->db->get_where('message_template', ['_key' => $key])->row()->template;
    echo $template;
  }

  public function update_template()
  {
    $id = $this->input->post('id');
    $template = $this->input->post('template');
    $this->db->update('message_template', ['template' => $template, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $id]);
    $this->session->set_flashdata('success_template', 'Update success!');
    redirect('msg_template');
  }

  public function send()
  {
    extract(PopulateForm());

    $splitRecipient = explode(',', $recipient);

    // set if CC is exist
    if (isset($cc)) {
      $splitCC = explode(',', $cc);
    }

    $send = $this->_send([
      'from' => $this->session->userdata('login_sess')['email'],
      'recipient' => $splitRecipient,
      'cc'        => isset($splitCC) ? $splitCC : '',
      'subject'   => $subject,
      'message'   => $message,
      'attach'    => isset($attachment) ? $populateAttachment : ''
    ]);

    $this->_save_message([
      '_key' => md5(date('YmdHis').$this->userid),
      'recipient' => serialize($splitRecipient),
      'cc' => isset($splitCC) ? serialize($splitCC) : '',
      'subject' => $subject,
      'sender' => $this->userid,
      'is_sent' => $send ? 1 : 0,
      'message' => strip_tags($message)
    ]);

    $send
      ? $this->session->set_flashdata('message_sent', 'Message successfully sent!')
      : $this->session->set_flashdata('message_fail', 'Message failed to send!');
    redirect('mail','refresh');
  }

  private function _send(array $mail) : bool
  {
    $config = array(
      'protocol'  => 'smtp',
      'smtp_host' => 'ssl://smtp.gmail.com',
      'smtp_port' => 465,
      'smtp_user' => SMTP_USER, //email id
      'smtp_pass' => SMTP_PASS,
      'mailtype'  => 'html',
      'charset'   => 'iso-8859-1'
    );

    $this->load->library('email', $config);
    $this->email->set_newline("\r\n");
    $this->email->from($mail['from'], $this->username);
    $this->email->to($mail['recipient']);
    !empty($mail['cc']) ? $this->email->cc($mail['cc']) : '';
    $this->email->subject($mail['subject']);
    $this->email->message($mail['message']);

    $result = $this->email->send();
    return $result;
  }

  private function _save_message(array $msg) : bool
  {
    $this->db->insert('message', $msg);
    return $this->db->affected_rows() ? TRUE : FALSE;
  }

  public function invite()
  {
    extract(PopulateForm());

    $send = $this->_send([
      'from' => $from,
      'recipient' => $recipient,
      'subject'   => $subject,
      'message'   => $message
    ]);

    $data = [
      'recipient' => $recipient,
      'subject' => $subject,
      'sender' => $this->userid
    ];

    $this->db->insert('invited', $data);
    $this->session->set_flashdata('message_sent', 'Invitation sent!');  
    redirect('mail','refresh');
  }

}
