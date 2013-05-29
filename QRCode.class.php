<?php
/**
 * @title            QR Code
 * @desc             Compatible to vCard 4.0 or higher.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>, modified by Yu Chen Hou<me@yuchenhou.com>
 * @copyright        (c) 2012, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License.
 * @version          1.2
 */

class QRCode
{

    const API_URL = 'http://chart.apis.google.com/chart?chs=';

    private $_sData;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_sData = 'BEGIN:VCARD' . "\n";
        $this->_sData .= 'VERSION:4.0' . "\n";
        return $this;
    }

    /**
     * URL address.
     *
     * @param string $sUrl
     * @return object this
     */
    public function url($sUrl)
    {
        $sUrl = (substr($sUrl, 0, 4) != 'http') ? 'http://' . $sUrl : $sUrl;
        $this->_sData .= 'URL:' . $sUrl . "\n";
        return $this;
    }

    /**
     * Generate the QR code.
     *
     * @return object this
     */
    public function finish()
    {
        $this->_sData .= 'END:VCARD';
        return $this;
    }

    /**
     * Get the URL of QR Code.
     *
     * @param integer $iSize Default 150
     * @param string $sECLevel Default L
     * @param integer $iMargin Default 1
     * @return string The API URL configure.
     */
    public function get($iSize = 150, $sECLevel = 'L', $iMargin = 1)
    {
        $this->_sData = urlencode($this->_sData);
        return static::API_URL . $iSize . 'x' . $iSize . '&amp;cht=qr&amp;chld=' . $sECLevel . '|' . $iMargin . '&amp;chl=' . $this->_sData;
    }

    /**
     * The HTML code for displaying the QR Code.
     *
     * @return void
     */
    public function display()
    {
        echo '<p class="center"><img src="' . $this->get() . '" alt="QR Code" /></p>';
    }

}
