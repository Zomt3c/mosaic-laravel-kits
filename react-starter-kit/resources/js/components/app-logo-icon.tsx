import type { SVGAttributes } from 'react';

/**
 * The app's logo: an M of mosaic tiles, one colour of the Mosaic banner per column. Replace it with the
 * project's own logo (and public/favicon.svg, favicon.ico, apple-touch-icon.png) when there is one.
 */
export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
            <rect x="1" y="1" width="6" height="6" rx="1.5" fill="#D8875F" />
            <rect x="29" y="1" width="6" height="6" rx="1.5" fill="#86AF86" />
            <rect x="1" y="8" width="6" height="6" rx="1.5" fill="#D8875F" />
            <rect x="8" y="8" width="6" height="6" rx="1.5" fill="#D9AE5D" />
            <rect x="22" y="8" width="6" height="6" rx="1.5" fill="#6087B0" />
            <rect x="29" y="8" width="6" height="6" rx="1.5" fill="#86AF86" />
            <rect x="1" y="15" width="6" height="6" rx="1.5" fill="#D8875F" />
            <rect x="15" y="15" width="6" height="6" rx="1.5" fill="#5EAEAE" />
            <rect x="29" y="15" width="6" height="6" rx="1.5" fill="#86AF86" />
            <rect x="1" y="22" width="6" height="6" rx="1.5" fill="#D8875F" />
            <rect x="29" y="22" width="6" height="6" rx="1.5" fill="#86AF86" />
            <rect x="1" y="29" width="6" height="6" rx="1.5" fill="#D8875F" />
            <rect x="29" y="29" width="6" height="6" rx="1.5" fill="#86AF86" />
        </svg>
    );
}
