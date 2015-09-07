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
            $profile->name = $info['name'] ?: null;
            $profile->email = $info['email'] ?: null;
            $profile->nickname = $info['nickname'] ?: null;
            $profile->first_name = $info['first_name'] ?: null;
            $profile->last_name = $info['last_name'] ?: null;
            $profile->location = $info['location'] ?: null;
            $profile->description = $info['description'] ?: null;
            $profile->image = $info['image'] ?: null;
            $profile->phone = $info['phone'] ?: null;
            $profile->url = $info['url'] ?: null;
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

}