<?php
/**
 * Содержит текст в наименовании.
 * @author Клюкин П. В.
 * @version $Id: String.php 01 2017-01-11 $
 */
 
class Basictypes_String implements Adaptor_DataType
{
	protected $value;
	
	public function __construct($value)
	{
		$this->setValue($value);
	}
	
	public function getValue($mode=Adaptor_DataType::INT)
	{
		switch($mode){
			case Adaptor_DataType::INT: return $this->value;
			case Adaptor_DataType::XSD: return $this->LogicalToXSD();
			case Adaptor_DataType::SQL: return $this->LogicalToSQL();
			default: trigger_error(__CLASS__."->".__METHOD__.": validation error: mode=".$mode);
		}
	}
	
	public function setValue($value)
	{
		$this->value = trim(strip_tags($value));
		return $this->value;
	}
	
	public function __toString()
	{
		return $this->value;
	}
	
	public function LogicalToXSD()
	{
		return $this->value;
	}
	
	public function LogicalToSQL()
	{
		return $this->value;
	}
}