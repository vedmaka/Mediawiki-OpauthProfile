<?php
/**
 * Hooks class declaration for mediawiki extension OpauthProfile
 *
 * @file OpauthProfile.hooks.php
 * @ingroup OpauthProfile
 */

class OpauthProfileHooks {

    /**
     * @param User $user
     * @param string $provider
     * @param array $info
     * @return bool
     */
    public static function onOpauthLoginUserCreated( $user, $provider, $info, $uid )
    {

        if( $user ) {

            $profile = new OpauthProfile( $user->getId() );
            $profile->name = isset($info['name']) ? $info['name'] : null;
            $profile->email = isset($info['email']) ? $info['email'] : null;
            $profile->nickname = isset($info['nickname']) ? $info['nickname'] : null;
            $profile->first_name = isset($info['first_name']) ? $info['first_name'] : null;
            $profile->last_name = isset($info['last_name']) ? $info['last_name'] : null;
            $profile->location = isset($info['location']) ? $info['location'] : null;
            $profile->description = isset($info['description']) ? $info['description'] : null;
            $profile->image = isset($info['image']) ? $info['image'] : null;
            $profile->phone = isset($info['phone']) ? $info['phone'] : null;
            $profile->url = isset($info['url']) ? $info['url'] : null;
            $profile->provider = $provider;
            $profile->uid = $uid;
            $profile->save();

        }

        return true;
    }

    /**
     * @param DatabaseUpdater $updater
     */
    public static function onLoadExtensionSchemaUpdates( $updater )
    {

        $updater->addExtensionTable(
            'opauth_user_profile',
            __DIR__ . '/schema/opauth_user_profile.sql'
        );

    }

	/**
	 * @param string $returnTo
	 * @param string $returnToQuery
	 * @param string $type
	 *
	 * @return bool
	 */
    public static function onPostLoginRedirect( &$returnTo, &$returnToQuery, &$type )
    {
		if( $type == 'signup' ) {
			$type = 'successredirect';
			$returnToQuery = '';
			$returnTo = "Special:UserProfile";
		}

		return true;
    }

	/**
	 * @param $redirectTarget
	 * @param $user
	 * @param $wasCreated
	 *
	 * @return bool
	 */
    public static function onOpauthLoginFinalRedirect( &$redirectTarget, $user, $wasCreated )
    {
    	if( $wasCreated ) {
    		$redirectTarget = SpecialPage::getTitleFor('UserProfile')->getFullURL('from_social=yes');
	    }

        return true;
    }

	/**
	 * @param User $user
	 * @param bool $byEmail
	 *
	 * @return bool
	 */
    public function onAddNewAccount( $user, $byEmail ) {
		if( $byEmail ) {
			if( $user ) {

				$profile = new OpauthProfile( $user->getId() );
				$profile->name = $user->getRealName() ? $user->getRealName() : null;
				$profile->email = $user->getEmail() ? $user->getEmail() : null;
				$profile->provider = 'local';
				$profile->uid = $user->getId();
				$profile->save();

			}
		}
	    return true;
    }

	/**
	 * @param Article $article
	 * @param bool $outputDone
	 * @param bool $pcache
	 *
	 * @return bool
	 */
	public static function onArticleViewHeader( &$article, &$outputDone, &$pcache ) {
		if( $article && $article->getTitle() && $article->getTitle()->getNamespace() == NS_USER ) {

			$article->getContext()->getOutput()->addModuleStyles('ext.OpauthProfile.main');

			$data = array(
				'contributions' => array(
					'edit_count' => 0,
					'revisions' => array()
				)
			);

			$user = User::newFromName( $article->getTitle()->getBaseText() );
			$user->load();
			if( !$user || $user->getId() < 1 ) {
				return true;
			}

			if( !OpauthProfile::exists( $user->getId() ) ) {
				return true;
			}

			$profile = new OpauthProfile( $user->getId() );

			$data['username'] = $user->getName();
			$data['name'] = $profile->name ? $profile->name : '-';
			$data['picture'] = $profile->image ? $profile->image : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIwAAACMCAYAAACuwEE+AAAFOUlEQVR4Xu3YZ0ujURCG4YkgFuyoiGLBiiJi+f+/QLGBqNjLBwvG3sCyzIGIyeqSwTEks7dfXHGYN/PMtScnZrLZ7LvwRQJFJpABTJFJUZYSAAwQTAkAxhQXxYDBgCkBwJjiohgwGDAlABhTXBQDBgOmBABjiotiwGDAlABgTHFRDBgMmBIAjCkuigGDAVMCgDHFRTFgMGBKADCmuCgGDAZMCQDGFBfFgMGAKQHAmOKiGDAYMCUAGFNcFAMGA6YEAGOKi2LAYMCUAGBMcVEMGAyYEgCMKS6KAYMBUwKAMcVFMWAwYEoAMKa4KAYMBkwJAMYUF8WAwYApAcCY4qIYMBgwJQAYU1wUAwYDpgQAY4qLYsBgwJQAYExxUQwYDJgSAIwpLooBgwFTAoAxxUUxYDBgSgAwprgoBgwGTAkAxhQXxYDBgCkBwJjiohgwGDAlABhTXBQDBgOmBABjiotiwGDAlABgTHFRDBgMmBIAjCkuigGDAVMCgDHFRTFgMGBKoOLBPD4+ytLSUhp6dnZWamtr8wLY2NiQ4+NjGR4eloGBgfS7g4MD2d3dldfXV2lsbJSJiYn0vZivUj+vmNdUypqKBfP+/i7n5+eyubkpz8/PUl9f/xeYbDYrq6ur8vLy8gHm+vpaVlZWpLm5OQHSfyuW6elpyWQy32Zf6ueVEoHlWRUL5vb2VhYXF0UX+fb2lk6WzyeMnh6K4fLyMtXkThg9Xba2tmRkZET6+/tlfn5enp6eZGZmJgHUk6ejoyPVLy8vp1NoampKqqqq3J/X0NBg2VVZ1FYsmLu7u7T47u5u0bed6urqPDCHh4eys7MjLS0tcnFx8QFmbW1Nzs7OZHx8XLq6uj5QTU5OplpF+PDwIE1NTQmbnkJDQ0PyG89rb28vCwSWF1GxYHJD5k6az2By94y6urqEQOHkTphCMIU/n5ycyPr6ejpZ9ASYm5tLGH/reZZllUNtSDB64ugpom8lV1dXsr29XdQJo//j9b6zsLCQTpS+vj4ZHR3N29NXQH/yvHJAYHkN4cDo8Lm3lcIg9JTRi+13dxg9Ufb29tI9Ru89NTU1CZ1ekL87YX76PMuyyqE2HJjCj9X7+/t5J4zeS/Qy3NraKoODg+liq1D0U9L9/X36iK6oOjs75ejoSNra2vI+QX11wnxepOV5//pUVg44vnoN/x0YDUEh6L1G335yf4dRNHqfOT09TZB6e3sTppubGxkbG5Oenp6UnxXMd88r9u8+5Qan4sGUW6DRXw9gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/YeT7AOAcavR1gom/Yeb4/HZAutcoP83oAAAAASUVORK5CYII=';
			$data['location'] = $profile->location ? $profile->location : '-';
			$data['website'] = $profile->url ? $profile->url : '-';
			$data['phone'] = $profile->phone ? $profile->phone : '-';

			// Gather user contributions
			// For now just list most recent user contributions
			$data['contributions']['edit_count'] = $user->getEditCount();
			$dbr = wfGetDB(DB_SLAVE);
			$result = $dbr->select(
				array('revision', 'page'),
				array('rev_page', 'rev_len', 'rev_parent_id', 'rev_comment', 'rev_timestamp', 'page_namespace', 'page_title'),
				array(
					'rev_user' => $user->getId(),
					'page_namespace' => 0
				),
				__METHOD__,
				array(
					'ORDER BY' => 'rev_timestamp DESC',
					'LIMIT' => 10
				),
				array(
					'page' => array(
						'INNER JOIN', array(
							'rev_page = page_id'
						)
					)
				)
			);

			while( $row = $result->fetchRow() ) {

				$item = array(
					'type' => 'pencil',
					'text' => 'edited', //$row['rev_comment'],
					'diff' => $row['rev_len'],
					'page_text' => Title::newFromID( $row['rev_page'] )->getBaseText(),
					'page_link' => Title::newFromID( $row['rev_page'] )->getFullURL(),
					'time' => date( 'j F Y', wfTimestamp( TS_UNIX, $row['rev_timestamp']) )
				);

				if( $row['rev_parent_id'] == 0 ) {
					$item['type'] = 'plus';
					$item['text'] = 'created';
				}

				$data['contributions']['revisions'][] = $item;
			}

			$data['has_edits'] = ($data['contributions']['edit_count'] > 0) ? true : false;
			$data['morelink'] = SpecialPage::getTitleFor('Contributions')->getFullURL().'/'.$user->getName();

			$templater = new TemplateParser( dirname(__FILE__).'/specials/templates/', true );
			$html = $templater->processTemplate( 'userpage', $data );
			$article->getContext()->getOutput()->addHTML($html);
			$outputDone = true;
		}
	}

}