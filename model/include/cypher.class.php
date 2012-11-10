<?php
/**
 * This PHP class provides very simple but a bit more secure way of Mcrypt
 * PHP extention using.
 *
 *
 * @author Alexander Zubakov <developer@xinit.ru>
 * @copyright © 2012 Alexander Zubakov
 */
class Cypher {
    /**
     * Cypher key.
     * @var string
     */
    protected $key = '';

    /**
     * Salt to use with key derivation function (KDF). Just use zero-octets
     * string because we don't need to hide password we just need to make it
     * random-like.
     * @var string
     */
    protected $KDF_salt = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

    /**
     * Hash algorithm to use with KDF. Use strong hash function to avoid
     * collisions.
     * @var string
     */
    protected $KDF_algo = 'sha256';

    /**
     * When encrypt or decrypt file then it will be read by corresponding
     * methods using blocks of this size. Default is 0.5 MB.
     * @var int
     */
    protected $file_blocksize = 524288;

    /**
     * Cypher descriptor returned by mcrypt_module_open().
     * @var string
     */
    protected $cypher;


    /**
     * Constructor. Initialise used cypher algorithm.
     *
     * @param string $algo Cypher algorithm. Should be one of the
     *                     mcrypt_list_algorithms() function output.
     * @param string $mode Block cypher mode. Should be one of the
     *                     mcrypt_list_modes() function output.
     */
    public function __construct($algo = 'twofish', $mode = 'cbc') {
        $this->cypher = mcrypt_module_open($algo, '', $mode, '');
    }


    /**
     * Denitialise used cypher algorithm.
     */
    public function __destruct() {
        mcrypt_module_close($this->cypher);
    }


    /**
     * Implement some properties.
     */
    public function __set($name, $value) {
        switch($name) {

            //set key (you can set only plain text key this way)
            case 'key':
                $this->setKey($value);
            break;

            //set salt to use in key derivation function (KDF)
            case 'KDF_salt':
                $this->KDF_salt = $value;
            break;

            //set hash algorithm to use in KDF. Use only algorithms that are
            //acceptable by hash_hmac() PHP function
            case 'KDF_algo':
                $this->KDF_algo = $value;
            break;

            //set size of block (in octets) data will be read or written at
            //once in encryptFile() and decryptFile() methods. Should be
            //multiple of 16 or nearest greater multiple of 16 will be used.
            case 'file_blocksize':
                $block_size = mcrypt_enc_get_block_size($this->cypher);
                while ($value++ % $block_size != 0);
                $this->file_blocksize = --$value;
            break;

            default:
                throw new Exception("No writable property ".__CLASS__."::$name.");
            break;
        }
    }


    /**
     * Key derivation function (HMAC).
     *
     * @param string $algo Hash algorithm.
     * @param string $P Password.
     * @param string $S Salt.
     * @param int $dkLen Derived key length (in octets).
     * @return string Derived key.
     */
    protected function kdf($algo, $P, $S, $dkLen) {
        $DK = '';
        while (strlen($DK) < $dkLen) {
            $DK .= hash_hmac($algo, $DK.$P, $S, true);
        }
        $DK = substr($DK, 0, $dkLen);

        return $DK;
    }


    /**
     * As we use block cypher then source dataset should be multiple of
     * block size, so source dataset will be appended with random octets to
     * proper length, last octet will tell how many octets was added.
     * If source length is already multiple of block size than one block
     * will be added with chr(block size) at the end.
     *
     * @param string $data The data buffer.
     * @return string Data ready to cypher.
     */
    protected function dataPrepare($data) {
        $block_size = mcrypt_enc_get_block_size($this->cypher);

        //length of last block
        $last_block_len = strlen($data) % $block_size;

        //append $data with random octets. If last block is less than
        //$block_size then last octet will tell how many octets added
        for ($i = 0; $i < $block_size - $last_block_len - 1; $i++) {
            $data .= chr(mt_rand(0, 255));
        }
        $data .= chr($i + 1);

        return $data;
    }


    /**
     * Create binary key of length compatible to
     * mcrypt_enc_get_supported_key_sizes() from text string of arbitrary
     * length using kdf() method.
     *
     * @param string $src_key Text representation of cypher key.
     * @return string Binary key. It will be always the same length or a
     *                bit more as $src_key.
     */
    protected function keyPrepare($src_key) {
        //default key length is source key length
        $key_length = strlen($src_key);

        //choose one of algorithm supperted sizes
        $key_sizes = mcrypt_enc_get_supported_key_sizes($this->cypher);
        if (sizeof($key_sizes) > 0) {
            //sort array of supported key sizes as we shouldn/t guess if it
            //is sorted or not
            sort($key_sizes);

            //find first key size that will be equal or greater than
            //$src_key
            for ($i = 0, $found = false; $i < sizeof($key_sizes) && !$found; $i++) {

                //key length we need
                if ($key_sizes[$i] >= $key_length) {
                    $key_length = $key_sizes[$i];
                    $found = true;
                }
            }
        }

        //key is bigger than maximum
        if ($key_length > mcrypt_enc_get_key_size($this->cypher)) {
            $key_length = mcrypt_enc_get_key_size($this->cypher);
        }

        $key = $this->kdf($this->KDF_algo, $src_key, $this->KDF_salt, $key_length);
        return $key;
    }


    /**
     * Assign key. You can use binary key of algorithm supported length or
     * simple string of text of arbitrary length. If you use simple text key
     * then it will be converted to acceptable length random-like
     * byte-string using key derivation function. If you use binary keys,
     * you must use byte string of correct length.
     *
     * @param string $key Key to encode data.
     * @param bool $raw_key If set to TRUE then $key is byte string with
     *                      correct key.
     */
    public function setKey($key, $raw_key = false) {
        if (!$raw_key) {
            $key = $this->keyPrepare($key);
        }

        $this->key = $key;
    }


    /**
     * Encrypt data. Note that result is always bigger than source. First
     * octets in the result set is Initialisation vector (IV) we need to
     * implement some encryption methods.
     *
     * @param string $data The data string to be encrypted.
     * @return string Encrypted data as raw byte string.
     */
    public function encrypt($data) {
        $data = $this->dataPrepare($data);

        //Initialisation Vector (IV)
        $encrypted = $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->cypher), MCRYPT_DEV_URANDOM);

        //encrypt data
        mcrypt_generic_init($this->cypher, $this->key, $iv);
        $encrypted .= mcrypt_generic($this->cypher, $data);
        mcrypt_generic_deinit($this->cypher);

        return $encrypted;
    }


    /**
     * Decrypt data.
     *
     * @param string $data The data string to be decrypted.
     * @return string Decrypted data.
     */
    public function decrypt($data) {
        //extract Initialization Vector (IV)
        $iv_size = mcrypt_enc_get_iv_size($this->cypher);
        $iv = substr($data, 0, $iv_size);
        $data = substr($data, $iv_size);

        //decrypt data
        mcrypt_generic_init($this->cypher, $this->key, $iv);
        $decrypted = mdecrypt_generic($this->cypher, $data);
        mcrypt_generic_deinit($this->cypher);

        //as last octet represents count of added octets to match required
        //message length, take it and cut message to initial length
        $decrypted = substr($decrypted, 0, -ord(substr($decrypted, -1, 1)));

        return $decrypted;
    }


    /**
     * Encrypt file. Note that result is always bigger than source. First 16
     * octets in the result set is Initialisation vector (IV) we need to
     * implement Cipher Block Chaining (CBC) encryption method.
     *
     * @param string $src Filepath to file being encrypted.
     * @param string $dest Filepath to encrypted file.
     */
    public function encryptFile($src, $dest) {
        if(($src_handle = fopen($src , 'rb')) === false) {
            throw new Exception("Can't read file `$src`.");
        }
        flock($src_handle, LOCK_SH);

        if (($dest_handle = fopen($dest, 'wb')) === false) {
            throw new Exception("Can't write to file `$dest`.");
        }
        flock($dest_handle, LOCK_EX);

        //Initialisation Vector (IV)
        $encrypted = $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->cypher), MCRYPT_DEV_URANDOM);

        //encode file reading it by chunks
        while (!feof($src_handle)) {
            $data = fread($src_handle, $this->file_blocksize);

            //chunk contains last block
            if (strlen($data) < $this->file_blocksize || feof($src_handle)) {
                $data = $this->dataPrepare($data);
            }

            //encrypt data
            mcrypt_generic_init($this->cypher, $this->key, $iv);
            $encrypted .= mcrypt_generic($this->cypher, $data);
            mcrypt_generic_deinit($this->cypher);

            fwrite($dest_handle, $encrypted);

            $encrypted = '';
        }

        flock($dest_handle, LOCK_UN);
        fclose($dest_handle);

        flock($src_handle, LOCK_UN);
        fclose($src_handle);
    }


    /**
     * Decrypt file.
     *
     * @param string $src Filepath to encrypted file.
     * @param string $dest Filepath to decrypted file.
     */
    public function decryptFile($src, $dest) {
        if(($src_handle = fopen($src , 'rb')) === false) {
            throw new Exception("Can't read file `$src`.");
        }
        flock($src_handle, LOCK_SH);

        if (($dest_handle = fopen($dest, 'wb')) === false) {
            throw new Exception("Can't write to file `$dest`.");
        }
        flock($dest_handle, LOCK_EX);

        //read Initialization Vector (IV)
        $iv = fread($src_handle, mcrypt_enc_get_iv_size($this->cypher));

        //last byte of decoded chunk to know count of appended octets at the
        //end of file decoding. Also keep in mind that decoded file will be
        //shorter by IV length and tail.
        $tail = mcrypt_enc_get_block_size($this->cypher);

        //decode file reading it by blocks
        while (!feof($src_handle)) {
            $data = fread($src_handle, $this->file_blocksize);

            //something read except eof
            if (strlen($data) > 0) {
                //decrypt data
                mcrypt_generic_init($this->cypher, $this->key, $iv);
                $decrypted = mdecrypt_generic($this->cypher, $data);
                mcrypt_generic_deinit($this->cypher);

                $tail = ord(substr($decrypted, -1, 1)) + mcrypt_enc_get_block_size($this->cypher);

                fwrite($dest_handle, $decrypted);
            }
        }

        //adjust file size
        ftruncate($dest_handle, filesize($src) - $tail);

        flock($dest_handle, LOCK_UN);
        fclose($dest_handle);

        flock($src_handle, LOCK_UN);
        fclose($src_handle);
    }
}
