import { Link } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import DesktopNav from './DesktopNav';
import MobileNav from './MobileNav';
import RoleBadge from '@/Components/RoleBadge';
import Dropdown from '@/Components/Dropdown';

export default function Navigation() {
    const { auth } = usePage().props;
    const user = auth?.user;

    return (
        <nav className="bg-white border-b border-gray-100">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between items-center h-16">
                    {/* Logo */}
                    <div className="flex-shrink-0">
                        <Link href="/">
                            <img src="/storage/imagesAccueil/Calque_1.svg" alt="Logo" className="w-24 h-24" />
                        </Link>
                    </div>

                    {/* Desktop Navigation */}
                    <div className="hidden md:flex md:items-center md:justify-center flex-1 px-8">
                        <DesktopNav />
                    </div>

                    {/* User Menu */}
                    <div className="hidden md:flex md:items-center">
                        <div className="flex items-center space-x-4">
                            {/* Location */}
                            <Link href="#" className="text-gray-500 hover:text-gray-700 flex items-center">
                                <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span className="ml-1">Angers</span>
                            </Link>

                            {/* Favorites */}
                            {user && (
                                <Link href={route('favorites.index')} className="text-gray-500 hover:text-gray-700">
                                    <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </Link>
                            )}

                            {/* Cart */}
                            {user && (
                                <Link href={route('cart.index')} className="text-gray-500 hover:text-gray-700">
                                    <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </Link>
                            )}

                            {/* User Account */}
                            {user ? (
                                <Dropdown>
                                    <Dropdown.Trigger>
                                        <button type="button" className="flex items-center space-x-2 text-gray-500 hover:text-gray-700">
                                            <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <div className="flex items-center">
                                                <span className="text-sm font-medium">{user.name}</span>
                                                <span className="ml-2">
                                                    <RoleBadge role={user.role} />
                                                </span>
                                            </div>
                                        </button>
                                    </Dropdown.Trigger>

                                    <Dropdown.Content>
                                        <Dropdown.Link href={route('profile.edit')}>
                                            Mon Profil
                                        </Dropdown.Link>
                                        <Dropdown.Link href={route('logout')} method="post" as="button">
                                            Déconnexion
                                        </Dropdown.Link>
                                    </Dropdown.Content>
                                </Dropdown>
                            ) : (
                                <Link href={route('login')} className="text-gray-500 hover:text-gray-700">
                                    <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </Link>
                            )}
                        </div>
                    </div>

                    {/* Mobile Navigation */}
                    <div className="md:hidden">
                        <MobileNav />
                    </div>
                </div>
            </div>
        </nav>
    );
} 