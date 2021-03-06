<?php
/**
 * Verify MediaWiki global function naming convention.
 * A global function's name must be prefixed with 'wf' or 'ef'.
 * Per https://www.mediawiki.org/wiki/Manual:Coding_conventions/PHP#Naming
 */
// @codingStandardsIgnoreStart
class MediaWiki_Sniffs_NamingConventions_PrefixedGlobalFunctionsSniff
	implements PHP_CodeSniffer_Sniff {
	// @codingStandardsIgnoreEnd

	public $ignoreList = [];

	public function register() {
		return [ T_FUNCTION ];
	}

	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$ptr = $stackPtr;
		while ( $ptr > 0 ) {
			$token = $tokens[$ptr];
			if ( $token['type'] === "T_NAMESPACE" && !isset( $token['scope_opener'] ) ) {
				// In the format of "namespace Foo;", which applies to the entire file
				return;
			}
			$ptr--;
		}
		$token = $tokens[$stackPtr];

		// Name of function
		$name = $tokens[$stackPtr + 2]['content'];

		if ( in_array( $name, $this->ignoreList ) ) {
			return;
		}

		// Check if function is global
		if ( $token['level'] == 0 ) {
			$prefix = substr( $name, 0, 2 );

			if ( $prefix !== 'wf' && $prefix !== 'ef' ) {
				// Forge a valid global function name
				$expected = 'wf' . ucfirst( $name );

				$error = 'Global function "%s" is lacking a \'wf\' prefix. Should be "%s".';
				$data = [ $name, $expected ];
				$phpcsFile->addError( $error, $stackPtr, 'wfPrefix', $data );
			}
		}
	}
}
