<?php

class SpecialUserProfile extends SpecialPage {

	private $templater;

	public function __construct() {
		parent::__construct( 'UserProfile' );
		$this->templater = new TemplateParser( dirname(__FILE__).'/templates/', true );
	}

	public function execute( $subPage ) {

		if( !$this->getUser()->isLoggedIn() ) {
			$this->displayRestrictionError();
		}

		$this->displayProfile();

	}

	private function displayProfile() {

		$data = array(
			'intro_text' => '',
			'profile_picture' => '',
			'profile_name' => '',
			'profile_location' => '',
			'profile_phone' => '',
			'profile_website' => ''
		);

		$user = $this->getUser();
		$profile = new OpauthProfile( $user->getId() );
		if( $profile ) {
			if( $profile->name ) {
				$data['profile_name'] = $profile->name;
			}
			if( $profile->image ) {
				$data['profile_picture'] = $profile->image;
			}
			if( $profile->location ) {
				$data['profile_location'] = $profile->location;
			}
			if( $profile->phone ) {
				$data['profile_phone'] = $profile->phone;
			}
			if( $profile->url ) {
				$data['profile_website'] = $profile->url;
			}
		}

		if( $this->getRequest()->getVal('from_social') == 'yes' ) {
			$data['intro_text'] = wfMessage('opauthprofile-profilepage-intro-from-social')->plain();
		}

		$html = $this->templater->processTemplate('profile', $data);
		$this->getOutput()->addHTML($html);

	}

}