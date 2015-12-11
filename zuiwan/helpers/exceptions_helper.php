<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!class_exists('InvalidInputException')) {
    class InvalidInputException extends Exception {
        protected $message = "Required field missing or invalid";
        protected $_detail;

        public function __construct($message="", $code=0, Exception $previous=NULL, $detail=NULL) {
            $this->_detail = $detail;
            parent::__construct($message, $code, $previous);
        }

        public function getDetail() {
            return $this->_detail;
        }
    }
}

if (!class_exists('InvalidStepException')) {
    class InvalidStepException extends Exception {
        protected $message = "Invalid step requested";
    }
}

if (!class_exists('UnsatisfiedException')) {
    class UnsatisfiedException extends Exception {
        protected $message = "Pre-condition not satisfied";
    }
}

if (!class_exists('PermissionDeniedException')) {
    class PermissionDeniedException extends Exception {
        protected $message = "Permission denied";
    }
}

if (!class_exists('InvalidIDException')) {
    class InvalidIDException extends Exception {
        protected $message = "Invalid version ID";
    }
}

if (!class_exists('DbException')) {
	class DbException extends Exception {
		public function __construct($message, $code, $previous,$sql=''){
            parent::__construct($message, $code, $previous);
            $this->sql = $sql;
		}
	}
}
