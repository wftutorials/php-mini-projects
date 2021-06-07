<?php


class TaskResult
{

  //task Listing
  const TASK_CALCULATOR = "calculate";

  public $started = "";
  public $completed = "";
  public $log = "";
  public $errors = "";
  public $output = "";
  public $files = [];

  /**
   * @return string
   */
  public function getOutput(): string
  {
    return $this->output;
  }

  /**
   * @param string $output
   */
  public function setOutput(string $output): void
  {
    $this->output = $output;
  }

  /**
   * @return array
   */
  public function getFiles(): array
  {
    return $this->files;
  }

  /**
   * @param array $files
   */
  public function setFiles(array $files): void
  {
    $this->files = $files;
  }




  /**
   * TaskResult constructor.
   */
  public function __construct()
  {
    $this->started = date("Y-m-d H:i:s");
  }


  public function close(){
    $this->setCompleted(date("Y-m-d H:i:s"));
  }

  public function addLog($msg){
    $this->log .= $msg . " (". date("Y-m-d H:i:s") . ") \r\n";
  }
  /**
   * @return false|string
   */
  public function getStarted()
  {
    return $this->started;
  }

  /**
   * @param false|string $started
   */
  public function setStarted($started): void
  {
    $this->started = $started;
  }

  /**
   * @return string
   */
  public function getCompleted(): string
  {
    return $this->completed;
  }

  /**
   * @param string $completed
   */
  public function setCompleted(string $completed): void
  {
    $this->completed = $completed;
  }

  /**
   * @return string
   */
  public function getLog(): string
  {
    return $this->log;
  }

  /**
   * @param string $log
   */
  public function setLog(string $log): void
  {
    $this->log = $log;
  }

  /**
   * @return string
   */
  public function getErrors(): string
  {
    return $this->errors;
  }

  /**
   * @param string $errors
   */
  public function setErrors(string $errors): void
  {
    $this->errors = $errors;
  }





}
