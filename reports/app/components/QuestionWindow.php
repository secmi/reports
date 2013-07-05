<?php

use Nette\Application\UI\Form;


/**
 * QuestionWindow component.
 */
class QuestionWindowControl extends Nette\Application\UI\Control
{
	
	/** @var Nette\Application\UI\Form */
	private $form;
	
	/** @var Nette\Http\Session */
	private $session;
	
	/** @var  string */
	private $question;
	
	/** @var  string */
	private $show = FALSE;
	
	

	/**
	 * @param Nette\Http\SessionSection $session
	 * @param callback|Nette\Callback $confirmHandler
	 */
	 public function __construct(\Nette\Http\SessionSection $session, $confirmHandler)
	{
		parent::__construct();
		
		$this->session = $session;

		$this->form = new Form($this, 'form');
		
		$this->form->addHidden('id');

		$this->session->confirmHandler = $confirmHandler;

		$this->form->addSubmit('yes', 'yes')
			->onClick[] = array($this, 'confirm');

		$this->form->addSubmit('no', 'no')
			->onClick[] = array($this, 'refused');
	}
	
	/**
	 * @return string
	 */
	protected function generateToken()
	{
		return md5(uniqid());
	}
	
	
	
	/**
	 * @param int $id
	 * @param string $question
	 */
	public function handleShowQuestion($id, $question)
	{
		
		$this->form['id']->value = $id;		
		$this->question = $question;
		$this->show = TRUE;
	}
	
	
	
	/**
	 * @param Nette\Forms\Controls\SubmitButton $button
	 */
	public function confirm($button)
	{
		$form = $button->getForm(TRUE);
		$values = $form->getValues();
	
		$this->show = FALSE;
		
		$callback = $this->session->confirmHandler;
		$args = array('id' => $values['id']);
			
		if(isset($this->session->confirmHandler)) unset($this->session->confirmHandler);
		if(isset($this->session->id)) unset($this->session->id);
		
		call_user_func_array($callback, $args);
		$this->presenter->redirect('this');
	}
	
	
	
	/**
	 * @param Nette\Forms\Controls\SubmitButton $button
	 */
	public function refused($button)
	{
		$form = $button->getForm(TRUE);
		$values = $form->getValues();
		
		$this->show = FALSE;
		
		if(isset($this->session->confirmHandler)) unset($this->session->confirmHandler);
		if(isset($this->session->id)) unset($this->session->id);
		
		$this->presenter->redirect('this');
	}
	
	

	public function render()
	{
		$this->template->setFile(__DIR__ . '/questionWindow.latte');
		
		$this->template->question = $this->question;
		$this->template->show = $this->show;
		
		return $this->template->render();
	}
}