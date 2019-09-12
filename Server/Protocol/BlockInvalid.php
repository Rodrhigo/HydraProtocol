<?php
include_once 'Protocol/IBlock.php';

class BlockInvalid extends Exception implements IBlock {
    public $StandardResponse;
    public $ResponseMessage;
    public $BlockID;

    public function BlockInvalid(int $NewBlockResponse, $Message = null,$BlockID = null) {
        $this->StandardResponse = $NewBlockResponse;
        $this->ResponseMessage = $Message;
    }

    public function ToResponse() {
        return "§".Block::GetBlockName($this->BlockID)."\n" . "StandardResponse:$this->StandardResponse\n" . (strlen($this->ResponseMessage . '') > 0 ? "ResponseMessage:$this->ResponseMessage\n" : "") . Block::GetBlockName($this->BlockID)."§";
    }

    public function GetLines() {


    }
}
