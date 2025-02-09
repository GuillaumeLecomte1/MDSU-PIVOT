import { Link, usePage } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import { useState } from 'react';
import Dropdown from '@/Components/Dropdown';
import RoleBadge from '@/Components/RoleBadge';

export default function Navigation() {
    const { auth } = usePage().props;
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    const isActive = (path) => {
        return window.location.pathname.startsWith(path);
    };

    return (
        <nav className="bg-[#F2F2F2] border-b border-gray-100">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between h-16">
                    <div className="flex">
                        <div className="shrink-0 flex items-center">
                            <Link href="/">
                                <img src="/storage/imagesAccueil/Calque_1.svg" alt="Logo" className="w-24 h-24" />
                                {/* <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800" /> */}
                            </Link>
                        </div>

                        <div className="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <Link
                                href="/"
                                className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out ${
                                    isActive('/') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                Accueil
                            </Link>

                            <Link
                                href={route('products.index')}
                                className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out ${
                                    isActive('/products') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                Produits
                            </Link>

                            <Link
                                href={route('ressourceries.index')}
                                className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out ${
                                    isActive('/ressourceries') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                Ressourceries
                            </Link>

                            <Link
                                href={route('categories.index')}
                                className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out ${
                                    isActive('/categories') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                Catégories
                            </Link>

                            {auth.user?.is_admin && (
                                <Link
                                    href={route('admin.dashboard')}
                                    className={`inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out ${
                                        isActive('/admin') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    }`}
                                >
                                    Administration
                                </Link>
                            )}
                        </div>
                    </div>

                    <div className="hidden sm:flex sm:items-center sm:ml-6">
                        <Link
                            href={route('favorites.index')}
                            className={`px-3 py-2 text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out ${
                                isActive('/favorites') ? 'text-gray-900' : ''
                            }`}
                        >
                            Mes Favoris
                        </Link>

                        {auth.user?.is_admin && (
                            <Link
                                href="/cart"
                                className="px-3 py-2 text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out"
                            >
                              Panier
                            </Link>
                        )}

                        <div className="ml-3 relative">
                            <Dropdown>
                                <Dropdown.Trigger>
                                    <span className="inline-flex rounded-md">
                                        <button type="button" className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            <div className="flex items-center">
                                                <span>{auth.user.name}</span>
                                                <span className="ml-2">
                                                    <RoleBadge role={auth.user.role} />
                                                </span>
                                            </div>
                                        </button>
                                    </span>
                                </Dropdown.Trigger>

                                <Dropdown.Content>
                                    <Dropdown.Link href={route('profile.edit')}>
                                        Profile
                                    </Dropdown.Link>
                                    {auth.user?.is_admin && (
                                        <Dropdown.Link href={route('admin.dashboard')}>
                                            Administration
                                        </Dropdown.Link>
                                    )}
                                    <Dropdown.Link href={route('logout')} method="post" as="button">
                                        Déconnexion
                                    </Dropdown.Link>
                                </Dropdown.Content>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    );
} 