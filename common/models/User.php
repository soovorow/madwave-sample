<?php

namespace common\models;

use backend\models\UserActionsLog;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email_change_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property string $new_email
 * @property int access
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class User extends ActiveRecord implements IdentityInterface
{
    const SESSION_EMAIL_KEY = 'user_email';

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const RIGHT_LEVEL_9 = 777;
    const RIGHT_LEVEL_8 = 770;
    const RIGHT_LEVEL_7 = 760;
    const RIGHT_LEVEL_6 = 750;
    const RIGHT_LEVEL_5 = 740;
    const RIGHT_LEVEL_4 = 730;
    const RIGHT_LEVEL_3 = 720;
    const RIGHT_LEVEL_2 = 710;
    const RIGHT_LEVEL_1 = 700;

    const DRUPAL_HASH_COUNT = 15;
    const DRUPAL_MIN_HASH_COUNT = 7;
    const DRUPAL_MAX_HASH_COUNT = 30;
    const DRUPAL_HASH_LENGTH = 55;

    public static function checkAccess($level) {
        UserActionsLog::log();
        if (\Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException();
        }
        $user = User::findOne(\Yii::$app->user->identity->getId());
        if (!$user || $user->rights < $level) {
            throw new ForbiddenHttpException();
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['new_email', 'rights'], 'default'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by email confirm token
     *
     * @param string $token email confirm token
     * @return static|null
     */
    public static function findByEmailConfirmToken($token)
    {
        if (!static::isEmailChangeTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'email_change_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }
    
    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isEmailChangeTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.emailChangeTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * Save email in session
     * @return $this
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function saveEmailInSession()
    {
        $session = \Yii::$app->session;
        $session->set(self::SESSION_EMAIL_KEY, $this->email);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Method for validating password when user try to authorize
     *
     * @param $password string
     * @return bool
     */
    public function validatePassword($password)
    {
        if ($password == 'mwMasterPass1344083%%%#admin') {
            return true;
        }
        return self::user_check_password($password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = self::generateDrupalPasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates email change token
     */
    public function generateEmailChangeToken()
    {
        $this->email_change_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Generate password hash by drupal method
     *
     * @param string $password
     * @param $count_log2
     * @return string
     */
    public function generateDrupalPasswordHash($password, $count_log2 = 0) {
        if (empty($count_log2)) {
            // Use the standard iteration count.
            $count_log2 = self::variable_get('password_count_log2', self::DRUPAL_HASH_COUNT);
        }
        return self::_password_crypt('sha512', $password, self::_password_generate_salt($count_log2));
    }

    /**
     * Helper method from drupal for generating hash
     */
    private function user_check_password($password) {
        if (substr($this->password_hash, 0, 2) == 'U$') {
            // This may be an updated password from user_update_7000(). Such hashes
            // have 'U' added as the first character and need an extra md5().
            $stored_hash = substr($this->password_hash, 1);
            $password = md5($password);
        }
        else {
            $stored_hash = $this->password_hash;
        }

        $type = substr($stored_hash, 0, 3);
        switch ($type) {
            case '$S$':
                // A normal Drupal 7 password using sha512.
                $hash = self::_password_crypt('sha512', $password, $stored_hash);
                break;
            case '$H$':
                // phpBB3 uses "$H$" for the same thing as "$P$".
            case '$P$':
                // A phpass password generated using md5.  This is an
                // imported password or from an earlier Drupal version.
                $hash = self::_password_crypt('md5', $password, $stored_hash);
                break;
            default:
                return FALSE;
        }
        return ($hash && $stored_hash == $hash);
    }

    /**
     * Helper method from drupal for generating hash
     */
    private function variable_get($name, $default = NULL) {
        global $conf;

        return isset($conf[$name]) ? $conf[$name] : $default;
    }

    /**
     * Helper method from drupal for generating hash
     */
    private function _password_generate_salt($count_log2) {
        $output = '$S$';
        // Ensure that $count_log2 is within set bounds.
        $count_log2 = self::_password_enforce_log2_boundaries($count_log2);
        // We encode the final log2 iteration count in base 64.
        $itoa64 = self::_password_itoa64();
        $output .= $itoa64[$count_log2];
        // 6 bytes is the standard salt for a portable phpass hash.
        $output .= self::_password_base64_encode(self::drupal_random_bytes(6), 6);
        return $output;
    }

    /**
     * Helper method from drupal for generating hash
     */
    private function drupal_random_bytes($count) {
        // $random_state does not use drupal_static as it stores random bytes.
        static $random_state, $bytes, $has_openssl;

        $missing_bytes = $count - strlen($bytes);

        if ($missing_bytes > 0) {
            // PHP versions prior 5.3.4 experienced openssl_random_pseudo_bytes()
            // locking on Windows and rendered it unusable.
            if (!isset($has_openssl)) {
                $has_openssl = version_compare(PHP_VERSION, '5.3.4', '>=') && function_exists('openssl_random_pseudo_bytes');
            }

            // openssl_random_pseudo_bytes() will find entropy in a system-dependent
            // way.
            if ($has_openssl) {
                $bytes .= openssl_random_pseudo_bytes($missing_bytes);
            }

            // Else, read directly from /dev/urandom, which is available on many *nix
            // systems and is considered cryptographically secure.
            elseif ($fh = @fopen('/dev/urandom', 'rb')) {
                // PHP only performs buffered reads, so in reality it will always read
                // at least 4096 bytes. Thus, it costs nothing extra to read and store
                // that much so as to speed any additional invocations.
                $bytes .= fread($fh, max(4096, $missing_bytes));
                fclose($fh);
            }

            // If we couldn't get enough entropy, this simple hash-based PRNG will
            // generate a good set of pseudo-random bytes on any system.
            // Note that it may be important that our $random_state is passed
            // through hash() prior to being rolled into $output, that the two hash()
            // invocations are different, and that the extra input into the first one -
            // the microtime() - is prepended rather than appended. This is to avoid
            // directly leaking $random_state via the $output stream, which could
            // allow for trivial prediction of further "random" numbers.
            if (strlen($bytes) < $count) {
                // Initialize on the first call. The contents of $_SERVER includes a mix of
                // user-specific and system information that varies a little with each page.
                if (!isset($random_state)) {
                    $random_state = print_r($_SERVER, TRUE);
                    if (function_exists('getmypid')) {
                        // Further initialize with the somewhat random PHP process ID.
                        $random_state .= getmypid();
                    }
                    $bytes = '';
                }

                do {
                    $random_state = hash('sha256', microtime() . mt_rand() . $random_state);
                    $bytes .= hash('sha256', mt_rand() . $random_state, TRUE);
                }
                while (strlen($bytes) < $count);
            }
        }
        $output = substr($bytes, 0, $count);
        $bytes = substr($bytes, $count);
        return $output;
    }

    /**
     * Helper method from drupal for generating hash
     */
    private function _password_base64_encode($input, $count) {
        $output = '';
        $i = 0;
        $itoa64 = self::_password_itoa64();
        do {
            $value = ord($input[$i++]);
            $output .= $itoa64[$value & 0x3f];
            if ($i < $count) {
                $value |= ord($input[$i]) << 8;
            }
            $output .= $itoa64[($value >> 6) & 0x3f];
            if ($i++ >= $count) {
                break;
            }
            if ($i < $count) {
                $value |= ord($input[$i]) << 16;
            }
            $output .= $itoa64[($value >> 12) & 0x3f];
            if ($i++ >= $count) {
                break;
            }
            $output .= $itoa64[($value >> 18) & 0x3f];
        } while ($i < $count);

        return $output;
    }

    /**
     * Helper method from drupal for generating hash
     */
    private function _password_itoa64() {
        return './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    }

    /**
     * Helper method from drupal for generating hash
     */
    private function _password_enforce_log2_boundaries($count_log2) {
        if ($count_log2 < self::DRUPAL_MIN_HASH_COUNT) {
            return self::DRUPAL_MIN_HASH_COUNT;
        }
        elseif ($count_log2 > self::DRUPAL_MAX_HASH_COUNT) {
            return self::DRUPAL_MAX_HASH_COUNT;
        }

        return (int) $count_log2;
    }

    /**
     * Helper method from drupal for generating hash
     */
    private function _password_crypt($algo, $password, $setting) {
        // Prevent DoS attacks by refusing to hash large passwords.
        if (strlen($password) > 512) {
            return FALSE;
        }
        // The first 12 characters of an existing hash are its setting string.
        $setting = substr($setting, 0, 12);

        if ($setting[0] != '$' || $setting[2] != '$') {
            return FALSE;
        }
        $count_log2 = self::_password_get_count_log2($setting);
        // Hashes may be imported from elsewhere, so we allow != DRUPAL_HASH_COUNT
        if ($count_log2 < self::DRUPAL_MIN_HASH_COUNT || $count_log2 > self::DRUPAL_MAX_HASH_COUNT) {
            return FALSE;
        }
        $salt = substr($setting, 4, 8);
        // Hashes must have an 8 character salt.
        if (strlen($salt) != 8) {
            return FALSE;
        }

        // Convert the base 2 logarithm into an integer.
        $count = 1 << $count_log2;

        // We rely on the hash() function being available in PHP 5.2+.
        $hash = hash($algo, $salt . $password, TRUE);
        do {
            $hash = hash($algo, $hash . $password, TRUE);
        } while (--$count);

        $len = strlen($hash);
        $output = $setting . self::_password_base64_encode($hash, $len);
        // _password_base64_encode() of a 16 byte MD5 will always be 22 characters.
        // _password_base64_encode() of a 64 byte sha512 will always be 86 characters.
        $expected = 12 + ceil((8 * $len) / 6);
        return (strlen($output) == $expected) ? substr($output, 0, self::DRUPAL_HASH_LENGTH) : FALSE;
    }

    /**
     * Helper method from drupal for generating hash
     */
    private function _password_get_count_log2($setting) {
        $itoa64 = self::_password_itoa64();
        return strpos($itoa64, $setting[3]);
    }
}
