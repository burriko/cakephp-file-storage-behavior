<?php

App::uses('StorageTypeInterface', 'CakeFileStorage.StorageType');

class FilesystemStorageType implements StorageTypeInterface
{
	protected $settings;

	public function __construct(Model $model, array $config)
	{
		$this->model = $model;
		$this->settings = $config;
	}

	/**
	 * Fetch a file's meta data without the file contents
	 *
	 * @param  int $id Record id
	 * @return array   File meta data
	 */
	public function fetchFileMetaData($id)
	{
		if ($record = $this->model->findById($id, array('id', 'filename', 'type', 'size'))) {
			return $record[$this->model->alias];
		} else {
			return false;
		}
	}

	/**
	 * Fetch a file's contents
	 *
	 * @param  array $meta_data File meta data
	 * @return string           File contents
	 */
	public function fetchFileContents($meta_data)
	{
		$file_path = $this->settings['file_path'];
		$file_path .= DS . $meta_data['filename'];

		if ( ! is_readable($file_path)) {
			return false;
		}

		return file_get_contents($file_path);
	}

	/**
	 * Store file
	 *
	 * @param array $file_data
	 * @return bool
	 */
	public function storeFile($file_data)
	{
		$folder = $this->settings['file_path'];

		if ( ! $this->isValidStorageFolder($folder)) {
			return false;
		}

		$path_and_filename = $folder . DS . $file_data['name'];
		return $this->storeFileInFolder($file_data['tmp_name'], $path_and_filename);
	}

	protected function storeFileInFolder($tmp_name, $real_name)
	{
		return move_uploaded_file($tmp_name, $real_name);
	}

	protected function isValidStorageFolder($folder)
	{
		return is_dir($folder) and is_writable($folder);
	}
}