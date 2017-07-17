<?php
$this->load->model('user/user_group');
// access - modify pavothemer edit
$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/pavothemer/edit');
$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/pavothemer/edit');
// access - modify pavothemer customize
$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/pavothemer/customize');
$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/pavothemer/customize');
// access - modify pavothemer sampledata
$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/sampledata');
$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/sampledata');
