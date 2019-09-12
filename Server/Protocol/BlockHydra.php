<?php
include_once 'Protocol/IBlock.php';
class BlockHydra implements IBlock{
    protected $Lines;
    /**
     * 
     * @param ProtocolResponse $ProtocolResponse
     * @param BlockLine[] $BlockLines
     */
    public function BlockHydra($BlockLines = null) {
        $this->Lines = $BlockLines;
    }
    
    public function ToResponse() {
	return "§Hydra\nMiName:MiValue\n§Hydra";
    }

    public function GetLines() {
        return $this->Lines;
    }
}

?>