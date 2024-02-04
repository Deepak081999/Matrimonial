<?php

namespace App\Controllers;

class Messages extends BaseController
{
    public $userroles;
    public $memberModel;
    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->session->start();
        helper('common');
        $uri = service('uri');
        if (!$this->session->get('is_login')) {
            header("Location:" . base_url());
            exit;
        } else if (!$this->session->user_details['active_role_id']) {
            header("Location:" . base_url("auth/loginAs"));
            exit;
        } else if (!$this->session->get('user_details')['complete_profile']) {
            header("Location:" . base_url("profile/editProfile/" . base64_encode($this->session->get('user_details')['id'])));
            exit;
        }
        $this->userroles = model('UserRoles');
        $this->memberModel = model('MembersProfileModel');
    }

    public function index()
    {
        switch ($this->session->user_details["active_role_id"]) {
            case 1:
                return $this->messages("sidebar_admin");
            case 2:
                return $this->messages("sidebar_moderator");
                break;
            case 3:
                return $this->messages("sidebar_member");
                break;
        }

    }

    public function messages($sidebar = "sidebar_member")
    {
        $data = [
            'session' => $this->session,
            'messages' => model('MembersProfileModel')->getUnseenMessages(),
            'sidebar' => $sidebar,
        ];
        return view('dashboard/content/messages', $data);
    }

    public function getMessages()
    {
        $id = $this->session->user_details['id'];
        $res = $this->memberModel->getMessages();
        $records = $this->memberModel->getCountNotification($id);
        $data = [];
        prd($res);
        foreach ($res as $val) {
            $name = $val->first_name . " " . $val->middle_name . "" . $val->last_name;
            $data[] = [
                $name,
                "<div class='m-0 hideextra pointer show_mess' name='$name' style='width:600px;' title='$val->msg' uid='$val->user_id'> " . $val->msg . "</div>",
                "<a class=' text-danger me-1 dle-mess-btn'  title='Delete user' uid='$val->id'><i class='fas fa-trash'></i></a>",

            ];
        }
        echo json_encode([
            "draw" => $this->request->getGet('draw'),
            "data" => $data,
            "recordsTotal" => $records,
            "recordsFiltered" => $records,

        ]);

    }

    public function msgSeen()
    {
        $mid = $this->request->getPost('mid');
        $res = $this->memberModel->msgSeen($mid);
        if ($res) {
            echo json_encode([
                "status" => 1,
                "msg" => "Msg Seen Successfully",
            ]);
        } else {
            echo json_encode([
                "status" => 0,
                "msg" => "Unsuccessful",
            ]);
        }
    }

    public function showMessages()
    {
        $id = $this->request->getPost('id');
        $details = $this->memberModel->getMessages($id);
        if ($details) {
            echo json_encode([
                "status" => 1,
                "msg" => "Messages fetch succesfully!",
                "info" => $details,
            ]);
        } else {
            echo json_encode([
                "status" => 0,
                "msg" => "Messages does not Fetch!",
            ]);
        }
    }
    public function deletemess()
    {

        $id = $this->request->getPost('id');
        $details = $this->memberModel->deleteMsg($id);
        echo json_encode([
            "status" => 1,
            "msg" => "Message deleted succesfully!",
        ]);
    }
}
