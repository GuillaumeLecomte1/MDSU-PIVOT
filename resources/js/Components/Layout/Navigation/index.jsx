import { Link } from '@inertiajs/react';
import DesktopNav from './DesktopNav';
import MobileNav from './MobileNav';

export default function Navigation() {
    return (
        <nav className="bg-white border-b border-gray-100">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between h-16">
                    {/* Logo */}
                    <div className="shrink-0 flex items-center">
                        <Link href="/">
                            <img src="/storage/imagesAccueil/Calque_1.svg" alt="Logo" className="w-24 h-24" />
                        </Link>
                    </div>

                    {/* Desktop Navigation */}
                    <DesktopNav />

                    {/* Mobile Navigation */}
                    <MobileNav />
                </div>
            </div>
        </nav>
    );
} 