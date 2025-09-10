<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Creates a password hash in the same way modern WordPress does.
 * It pre-hashes the password with a keyed HMAC-SHA384 before passing it to BCRYPT.
 *
 * @param string $password The plain-text password to hash.
 * @return string The WordPress-compatible password hash.
 */
if (!function_exists('create_wp_style_hash')) {
    function create_wp_style_hash($password) {
        // Step 1 & 2: Create a keyed SHA384 hash (the "pre-hash"). 
        // The key 'wp-sha384' MUST be identical to WordPress's key.
        $pre_hashed = hash_hmac('sha384', trim($password), 'wp-sha384', true);

        // Step 3: Base64-encode the pre-hashed password.
        $password_to_hash = base64_encode($pre_hashed);
        
        // Step 4: Use password_hash() on the Base64 string.
        $bcrypt_hash = password_hash($password_to_hash, PASSWORD_BCRYPT, ['cost' => 10]);

        // Step 5: Prepend the custom '$wp' identifier.
        return '$wp' . $bcrypt_hash;
    }
}

/**
 * Universal password verification function for CodeIgniter 3.
 * Handles:
 * 1. Modern WordPress BCRYPT hashes (with pre-hashing).
 * 2. Old WordPress PHPass MD5 hashes.
 * 3. Standard BCRYPT hashes without the '$wp' prefix.
 *
 * @param string $password The plain-text password to verify.
 * @param string $hash     The stored hash from the database.
 * @return bool            True if the password is correct, false otherwise.
 */
if (!function_exists('verify_password_universal')) {
    function verify_password_universal($password, $hash) {
        // --- LOGIC FOR MODERN WORDPRESS HASHES ($wp$2y$...) ---
        if (strpos($hash, '$wp$') === 0) {
            // Replicate the pre-hashing process on the user's submitted password
            $pre_hashed = hash_hmac('sha384', trim($password), 'wp-sha384', true);
            $password_to_verify = base64_encode($pre_hashed);

            // Strip the '$wp' prefix from the database hash
            $hash_to_check = substr($hash, 3);
            
            // Verify the pre-hashed password against the database hash
            return password_verify($password_to_verify, $hash_to_check);
        }

        // --- LOGIC FOR OLD WORDPRESS HASHES ($P$ or $H$) ---
        if (substr($hash, 0, 3) == '$P$' || substr($hash, 0, 3) == '$H$') {
            return _check_wp_phpass($password, $hash);
        }

        // --- FALLBACK FOR STANDARD BCRYPT HASHES ---
        // This handles hashes created by your old get_encrypted_password() method.
        if (password_verify($password, $hash)) {
            return true;
        }

        return false;
    }
}

/**
 * Verifies a password against a WordPress PHPass hash ($P$ or $H$).
 * This logic remains unchanged.
 */
if (!function_exists('_check_wp_phpass')) {
    function _check_wp_phpass($password, $stored_hash) {
        // ... (The code for _check_wp_phpass from the previous answer is unchanged and goes here)
        $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $count_log2 = strpos($itoa64, $stored_hash[3]);
        if ($count_log2 < 7 || $count_log2 > 30) return false;
        $count = 1 << $count_log2;
        $salt = substr($stored_hash, 4, 8);
        if (strlen($salt) != 8) return false;
        $hash = md5($salt . $password, true);
        do { $hash = md5($hash . $password, true); } while (--$count);
        $output = substr($stored_hash, 0, 12);
        $i = 0;
        $final_hash = '';
        do {
            $c1 = ord($hash[$i++]);
            $final_hash .= $itoa64[$c1 & 0x3f];
            if ($i < 16) { $c2 = ord($hash[$i]); $final_hash .= $itoa64[($c1 >> 6) | ($c2 << 2) & 0x3f]; } else { $final_hash .= $itoa64[($c1 >> 6) & 0x3f]; break; }
            if ($i++ >= 16) break;
            $c3 = ord($hash[$i]);
            $final_hash .= $itoa64[($c2 >> 4) | ($c3 << 4) & 0x3f];
            $final_hash .= $itoa64[($c3 >> 2) & 0x3f];
        } while (1);
        return hash_equals($output . $final_hash, $stored_hash);
    }
}