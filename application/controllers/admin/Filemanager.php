<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
ini_set('memory_limit', '-1');
// set max execution time 2 hours / mostly used for exporting PDF
ini_set('max_execution_time', 3600);

class Filemanager extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('settings_model');
    }

    public function index()
    {
        $data['title'] = lang('filemanager');
        $data['subview'] = $this->load->view('admin/filemanager/filemanager', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }
    public function elfinder_init()
    {
            $this->load->helper('path');
            $_allowed_files = explode('|', config_item('allowed_files'));
            $config_allowed_files = array();
            if (is_array($_allowed_files)) {
                foreach ($_allowed_files as $v_extension) {
                    array_push($config_allowed_files, '.' . $v_extension);
                }
            }

            $allowed_files = array();
            if (is_array($config_allowed_files)) {
                foreach ($config_allowed_files as $extension) {
                    $_mime = get_mime_by_extension($extension);

                    if ($_mime == 'application/x-zip') {
                        array_push($allowed_files, 'application/zip');
                    }
                    if ($extension == '.exe') {
                        array_push($allowed_files, 'application/x-executable');
                        array_push($allowed_files, 'application/x-msdownload');
                        array_push($allowed_files, 'application/x-ms-dos-executable');
                    }
                    array_push($allowed_files, $_mime);
                }
            }

            $filemanagerFolder = "filemanager";

            $root_options = array(
                'driver' => 'LocalFileSystem',
                'path' => set_realpath($filemanagerFolder),
                'URL' => site_url($filemanagerFolder) . '/',
                'uploadMaxSize' => config_item('max_file_size') . 'M',
                'accessControl' => 'access',
                'uploadAllow' => $allowed_files,
                'uploadDeny' => [
                    'application/x-httpd-php',
                    'application/php',
                    'application/x-php',
                    'text/php',
                    'text/x-php',
                    'application/x-httpd-php-source',
                    'application/perl',
                    'application/x-perl',
                    'application/x-python',
                    'application/python',
                    'application/x-bytecode.python',
                    'application/x-python-bytecode',
                    'application/x-python-code',
                    'wwwserver/shellcgi', // CGI
                ],
                'uploadOrder' => array(
                    'allow',
                    'deny'
                ),
                'attributes' => array(
                    array(
                        'pattern' => '/.tmb/',
                        'hidden' => true
                    ),
                    array(
                        'pattern' => '/.quarantine/',
                        'hidden' => true
                    ),
                    array(
                        'pattern' => '/public/',
                        'hidden' => true
                    ),
                    array(
                        'pattern' => '/common/',
                        'hidden' => true
                    )
                )
            );
            if ($this->session->userdata('user_type') == 3) {
                $user = $this->db->where('user_id', $this->session->userdata('user_id'))->get('tbl_users')->row();
                $path = set_realpath($filemanagerFolder . '/' . $user->media_path_slug);
                if (empty($user->media_path_slug)) {
                    $this->db->where('user_id', $user->user_id);
                    $slug = slug_it($user->username);
                    $this->db->update('tbl_users', array(
                        'media_path_slug' => $slug
                    ));
                    $user->media_path_slug = $slug;
                    $path = set_realpath($filemanagerFolder . '/' . $user->media_path_slug);
                }
                if (!is_dir($path)) {
                    mkdir($path);
                }
                if (!file_exists($path . '/index.html')) {
                    fopen($path . '/index.html', 'w');
                }
                array_push($root_options['attributes'], array(
                    'pattern' => '/.(' . $user->media_path_slug . '+)/', // Prevent deleting/renaming folder
                    'read' => true,
                    'write' => true,
                    'locked' => true
                ));
                $root_options['path'] = $path;
                $root_options['URL'] = site_url($filemanagerFolder . '/' . $user->media_path_slug) . '/';
            }

            $commonRootPath = $filemanagerFolder . '/common';
            $common_root = $root_options;
            $common_root['path'] = set_realpath($commonRootPath);
            $common_root['URL'] = site_url($filemanagerFolder) . '/common';
            unset($common_root['attributes'][3]);

            if (!is_dir($commonRootPath)) {
                mkdir($commonRootPath, 0755);
            }

            if (!file_exists($commonRootPath . '/index.html')) {
                $fp = fopen($commonRootPath . '/index.html', 'w');
                if ($fp) {
                    fclose($fp);
                }
            }

            if ($this->session->userdata('user_type') == 1) {
                $publicRootPath = $filemanagerFolder . '/public';
                $public_root = $root_options;
                $public_root['path'] = set_realpath($publicRootPath);
                $public_root['URL'] = site_url($filemanagerFolder) . '/public';
                unset($public_root['attributes'][3]);

                if (!is_dir($publicRootPath)) {
                    mkdir($publicRootPath, 0755);
                }

                if (!file_exists($publicRootPath . '/index.html')) {
                    $fp = fopen($publicRootPath . '/index.html', 'w');
                    if ($fp) {
                        fclose($fp);
                    }
                }

                $opts = array(
                    'roots' => array(
                        $root_options,
                        $common_root,
                        $public_root
                    )
                );
            } else {
              $opts = array(
                  'roots' => array(
                      $root_options,
                      $common_root
                  )
              );
            }

            $this->load->library('elfinder_lib', $opts);
    }

}
