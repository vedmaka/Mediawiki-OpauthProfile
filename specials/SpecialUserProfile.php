<?php

class SpecialUserProfile extends SpecialPage {

	private $templater;

	public function __construct() {
		parent::__construct( 'UserProfile' );
		$this->templater = new TemplateParser( dirname(__FILE__).'/templates/', true );
	}

	public function execute( $subPage ) {

		$this->displayProfile( true );

	}

	private function displayProfile( $editable = false ) {

		$data = array(
			'editable' => $editable,
			'intro_text' => ''
		);

		if( $this->getRequest()->getVal('from_social') == 'yes' ) {
			$data['intro_text'] = wfMessage('opauthprofile-profilepage-intro-from-social')->plain();
		}

		$html = $this->templater->processTemplate('profile', $data);
		$this->getOutput()->addHTML($html);

	}

}