<?php

namespace IMEdge\Web\Device;

use gipfl\IcingaWeb2\Img;
use IMEdge\Web\Data\Model\SnmpSystemInfo;

class DeviceVendor
{
    protected const VENDOR_OID_MAP = [
        '1.3.6.1.4.1.9'     => 'cisco.svg',
        '1.3.6.1.4.1.11'    => 'hp.svg',
        '1.3.6.1.4.1.43'    => '3com.svg',
        '1.3.6.1.4.1.171'   => 'd-link.svg',
        '1.3.6.1.4.1.311'   => 'microsoft.svg',
        '1.3.6.1.4.1.232'   => 'compaq.svg',
        '1.3.6.1.4.1.236'   => 'samsung.svg',
        '1.3.6.1.4.1.674'   => 'dell.svg',
        '1.3.6.1.4.1.890'   => 'zyxel.png',
        '1.3.6.1.4.1.1916'  => 'extreme-networks.png',
        '1.3.6.1.4.1.1991'  => 'extreme-networks.png', // Foundry -> Broadcom -> Extreme Networks
        '1.3.6.1.4.1.2011'  => 'huawei.svg',
        '1.3.6.1.4.1.2435'  => 'brother.svg',
        '1.3.6.1.4.1.2636'  => 'juniper.svg',
        '1.3.6.1.4.1.4329.15' => 'extreme-networks.png', // Siemens Hipath Wireless
        '1.3.6.1.4.1.4413'  => 'broadcom.svg',
        '1.3.6.1.4.1.5597'  => 'meinberg.png',
        '1.3.6.1.4.1.5624'  => 'extreme-networks.png', // Enterasys
        '1.3.6.1.4.1.7745'  => 'aethra.png',
        // '1.3.6.1.4.1.8072.3.2.1' => 'hpux9.svg',
        '1.3.6.1.4.1.8072.3.2.2'  => 'sunos4.svg',
        '1.3.6.1.4.1.8072.3.2.3'  => 'solaris.svg',
        '1.3.6.1.4.1.8072.3.2.8'  => 'freebsd.png',
        '1.3.6.1.4.1.8072.3.2.10' => 'linux.svg',
        '1.3.6.1.4.1.8072.3.2.13' => 'windows.svg', // only win32, NT (+Intel64)?
        '1.3.6.1.4.1.8741'  => 'sonicwall.svg',
        '1.3.6.1.4.1.10704' => 'barracuda.svg', // PHION-MIB
        '1.3.6.1.4.1.12356' => 'fortinet.svg',
        '1.3.6.1.4.1.14988' => 'mikrotik.svg',
        '1.3.6.1.4.1.16177' => 'westermo.svg',
        '1.3.6.1.4.1.16744' => 'blankom.svg',
        '1.3.6.1.4.1.18334' => 'konica-minolta.svg',
        '1.3.6.1.4.1.30065' => 'arista.svg',
        // '1.3.6.1.4.1.31192' => '', //  -> hella aglaia -> Automatic People Counting Device
        '1.3.6.1.4.1.50536' => 'truenas.svg', // -> FREENAS-MIB
        '1.3.6.1.4.1.52642' => 'fs-com.svg', // -> FS-SMI, FS-AG-MBM-MIB, FS-MEMORY-MIB, FS-PROCESS-MIB
    ];

    protected static function createLogoImg(string $filename): Img
    {
        return Img::create('img/imedge/vendor/vendorLogo/' . $filename, null, [
            'class' => 'vendor-logo'
        ]);
    }

    public static function getVendorLogo($row): ?Img
    {
        if ($row === null) {
            return null;
        }

        if ($row instanceof SnmpSystemInfo) {
            $row = (object) [
                'system_oid' => $row->get('system_oid'),
                'system_description' => $row->get('system_description'),
            ];
        }
        if (
            str_starts_with($row->system_oid ?? '', '1.3.6.1.4.1.25506.')
            && str_contains($row->system_description ?? '', 'Hewlett Packard Enterprise')
        ) {
            // H3C is exclusive provider of HPE® servers, storage and associated technical services
            return self::createLogoImg('hpe.svg');
        }
        if (
            $row->system_oid === '1.3.6.1.4.1.4413' /* BroadCom */
            && str_contains($row->system_description ?? '', 'EdgeSwitch 24')
        ) {
            return self::createLogoImg('ubiquiti.png');
        }

        foreach (self::VENDOR_OID_MAP as $oid => $logo) {
            if ($row->system_oid === $oid || str_starts_with($row->system_oid ?? '', "$oid.")) {
                return self::createLogoImg($logo);
            }
        }

        if (str_contains($row->system_description ?? '', 'Advanced Digital Broadcast')) {
            return self::createLogoImg('adb-group.svg');
        }
        if (str_contains($row->system_name ?? '', 'Phoenix Broadband Technologies')) {
            return self::createLogoImg('phoenix-broadband-technologies.svg');
        }
        // e.g. 	1.3.6.1.4.1.25506.11.1.81 -> HP, H3C?
        if (str_contains($row->system_description ?? '', 'Hewlett-Packard')) {
            return self::createLogoImg('hp.svg');
        }
        return null;
    }
}
