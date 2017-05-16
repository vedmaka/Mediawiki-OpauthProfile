<?php

class SpecialUserProfile extends SpecialPage {

	private $templater;

	public function __construct() {
		parent::__construct( 'UserProfile' );
		$this->templater = new TemplateParser( dirname(__FILE__).'/templates/', true );
	}

	public function execute( $subPage ) {

		$this->getOutput()->addModuleStyles('ext.OpauthProfile.main');
		$this->getOutput()->setPageTitle( wfMessage('opauthprofile-profilepage-special-edit-title')->plain() );

		if( !$this->getUser()->isLoggedIn() ) {
			$this->displayRestrictionError();
		}

		if( $this->getRequest()->wasPosted() ) {

			$picture = $this->getRequest()->getUpload('profile_picture');
			$name = $this->getRequest()->getVal('profile_name');
			$location = $this->getRequest()->getVal('profile_location');
			$phone = $this->getRequest()->getVal('profile_phone');
			$website = $this->getRequest()->getVal('profile_website');
			$interests = $this->getRequest()->getVal('profile_interests');

			$profile = new OpauthProfile( $this->getUser()->getId() );

			if( $name ) {
				$profile->name = $name;
			}

			if( $location ) {
				$profile->location = $location;
			}

			if( $phone ) {
				$profile->phone = $phone;
			}

			if( $website ) {
				$profile->url = $website;
			}

			if( $interests ) {
				$profile->interests = $interests;
			}

			// Picture
			if( !$picture->getError() ) {
				$uploader = new UploadFromFile();
				$uploader->initialize( md5($this->getUser()->getName().'_avatar'), $picture );
				$verify = $uploader->verifyUpload();
				if( $verify['status'] == UploadBase::OK ) {
					$status = $uploader->performUpload('user avatar uploaded', '', false, $this->getUser());
					if( $status->isGood() ) {
						$picture_url = $uploader->getLocalFile()->createThumb(100);
						$profile->image = $picture_url;
					}
				}
			}

			$profile->save();

		}

		$this->displayProfile( $this->getRequest()->wasPosted() );

	}

	private function displayProfile( $badge = false ) {

		$data = array(
			'intro_text' => '',
			'profile_picture' => '',
			'profile_name' => '',
			'profile_location' => '',
			'profile_phone' => '',
			'profile_website' => '',
			'profile_interests' => '',
			'badge' => $badge,
			'badge_text' => wfMessage('opauthprofile-profilepage-special-edit-badge')->plain(),
			'external_link' => $this->getUser()->getUserPage()->getFullURL()
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
			if( $profile->interests ) {
				$data['profile_interests'] = $profile->interests;
			}
		}

		if( $this->getRequest()->getVal('from_social') == 'yes' ) {
			$data['intro_text'] = wfMessage('opauthprofile-profilepage-intro-from-social')->plain();
		}

		$html = $this->templater->processTemplate('profile', $data);
		$this->getOutput()->addHTML($html);

	}

}