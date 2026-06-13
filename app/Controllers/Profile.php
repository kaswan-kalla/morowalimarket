<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Controller Profil User
 */
class Profile extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $user = $this->userModel->find($this->session->get('user_id'));
        return view('layout/marketplace_content', ['content' => 'profile', 'meta_title' => 'Profil Saya', 'user' => $user]);
    }

    /**
     * Update profil (AJAX)
     */
    public function update()
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $userId = $this->session->get('user_id');
        $rules = [
            'name'  => 'required|min_length[3]|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => false, 'message' => implode('<br>', $this->validator->getErrors())]);
        }

        $this->userModel->update($userId, [
            'name'  => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
        ]);

        $this->session->set('user_name', $this->request->getPost('name'));

        return $this->response->setJSON(['status' => true, 'message' => 'Profil berhasil diperbarui']);
    }

    /**
     * Upload foto profil (AJAX)
     */
    public function updatePhoto()
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $file = $this->request->getFile('photo');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pilih file foto']);
        }

        $userId = $this->session->get('user_id');
        $user   = $this->userModel->find($userId);

        // Hapus foto lama
        if ($user['photo']) delete_image($user['photo']);

        $photoPath = upload_image($file, 'uploads/users');
        $this->userModel->update($userId, ['photo' => $photoPath]);
        $this->session->set('user_photo', $photoPath);

        return $this->response->setJSON(['status' => true, 'message' => 'Foto profil diperbarui', 'data' => ['photo' => $photoPath]]);
    }

    /**
     * Ganti password (AJAX)
     */
    public function changePassword()
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $userId   = $this->session->get('user_id');
        $user     = $this->userModel->find($userId);
        $current  = $this->request->getPost('current_password');
        $newPass  = $this->request->getPost('new_password');

        if (!password_verify($current, $user['password'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Password lama tidak benar']);
        }

        if (strlen($newPass) < 6) {
            return $this->response->setJSON(['status' => false, 'message' => 'Password baru minimal 6 karakter']);
        }

        $this->userModel->update($userId, ['password' => password_hash($newPass, PASSWORD_BCRYPT)]);

        return $this->response->setJSON(['status' => true, 'message' => 'Password berhasil diubah']);
    }
}
