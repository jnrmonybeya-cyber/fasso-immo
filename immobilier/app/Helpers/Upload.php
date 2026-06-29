<?php
namespace App\Helpers;

class Upload {
    private $errors = [];
    private $uploadedFiles = [];
    
    public function validate($file) {
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            $this->errors[] = "Aucun fichier sélectionné.";
            return false;
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = "Erreur lors de l'upload du fichier.";
            return false;
        }
        
        // Vérification de la taille
        if ($file['size'] > MAX_FILE_SIZE) {
            $this->errors[] = "Le fichier est trop volumineux (max " . (MAX_FILE_SIZE / 1024 / 1024) . " Mo).";
            return false;
        }
        
        // Vérification du type MIME réel
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
        
        if (!in_array($mimeType, $allowedMimes)) {
            $this->errors[] = "Type de fichier non autorisé (JPEG, PNG, GIF, WEBP uniquement).";
            return false;
        }
        
        // Vérification de l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            $this->errors[] = "Extension de fichier non autorisée (jpg, jpeg, png, gif, webp uniquement).";
            return false;
        }
        
        return true;
    }
    
    public function upload($file, $subDir = '') {
        if (!$this->validate($file)) {
            return false;
        }
        
        $uploadDir = UPLOAD_DIR;
        if (!empty($subDir)) {
            $uploadDir .= $subDir . '/';
        }
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $this->uploadedFiles[] = $filename;
            return $filename;
        }
        
        $this->errors[] = "Erreur lors du déplacement du fichier.";
        return false;
    }
    
    public function uploadMultiple($files, $subDir = '') {
        $uploaded = [];
        $errors = [];
        
        if (!isset($files['name']) || !is_array($files['name'])) {
            $this->errors[] = "Format de données invalide.";
            return [];
        }
        
        $fileCount = count($files['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
            
            $result = $this->upload($file, $subDir);
            
            if ($result) {
                $uploaded[] = $result;
            } else {
                $errors[] = "Erreur pour le fichier " . $file['name'] . ": " . implode(', ', $this->getErrors());
                $this->clearErrors();
            }
        }
        
        $this->uploadedFiles = $uploaded;
        return $uploaded;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function clearErrors() {
        $this->errors = [];
    }
    
    public function getUploadedFiles() {
        return $this->uploadedFiles;
    }
    
    public static function deleteFile($filename, $subDir = '') {
        $filePath = UPLOAD_DIR;
        if (!empty($subDir)) {
            $filePath .= $subDir . '/';
        }
        $filePath .= $filename;
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}