import { Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Login({ status, canResetPassword }) {
    const [error, setError] = useState(null);
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = async (e) => {
        e.preventDefault();
        setError(null);

        post(route('login'), {
            onSuccess: () => {
                // Success is handled by the redirect
            },
            onError: (errors) => {
                if (errors.email) {
                    setError(errors.email);
                } else if (errors.password) {
                    setError(errors.password);
                } else {
                    setError('Une erreur est survenue lors de la connexion. Veuillez réessayer.');
                }
            },
            preserveScroll: true,
        });
    };

    return (
        <div className="min-h-screen flex">
            {/* Left Side - Green Background with Text */}
            <div className="hidden lg:flex lg:w-1/2 bg-emerald-400 p-12 flex-col justify-between">
                <div>
                    <h1 className="text-white text-4xl font-bold mb-6">Ravi de vous revoir !</h1>
                    <p className="text-white text-lg mb-8">
                        Connectez-vous pour accéder à votre espace et gérer vos produits sur notre plateforme.
                    </p>
                    
                    {/* Steps */}
                    <div className="space-y-8 mt-12">
                        <div className="flex items-start space-x-4">
                            <div className="w-8 h-8 rounded-full bg-white text-emerald-400 flex items-center justify-center font-bold">01</div>
                            <div>
                                <h3 className="text-white font-semibold text-lg">Gagnez un temps fou !</h3>
                                <p className="text-white opacity-90">Augmentez vos ventes en gérant votre visibilité physique et virtuel</p>
                            </div>
                        </div>

                        <div className="flex items-start space-x-4">
                            <div className="w-8 h-8 rounded-full bg-white text-emerald-400 flex items-center justify-center font-bold">02</div>
                            <div>
                                <h3 className="text-white font-semibold text-lg">Offrez-vous une super visibilité</h3>
                                <p className="text-white opacity-90">Avec une page qui différencie</p>
                            </div>
                        </div>

                        <div className="flex items-start space-x-4">
                            <div className="w-8 h-8 rounded-full bg-white text-emerald-400 flex items-center justify-center font-bold">03</div>
                            <div>
                                <h3 className="text-white font-semibold text-lg">Offrez-vous une super visibilité</h3>
                                <p className="text-white opacity-90">Avec une page qui référencie</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Right Side - Login Form */}
            <div className="w-full lg:w-1/2 bg-white p-8 lg:p-12">
                <div className="max-w-md mx-auto">
                    <h2 className="text-2xl font-bold text-gray-900 mb-8">Connexion</h2>
                    <p className="text-gray-600 mb-8">Connectez-vous pour accéder à votre espace.</p>

                    {status && (
                        <div className="mb-4 font-medium text-sm text-emerald-600">
                            {status}
                        </div>
                    )}

                    {error && (
                        <div className="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
                            <p className="text-sm text-red-600">{error}</p>
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-6">
                        {/* Email */}
                        <div>
                            <label htmlFor="email" className="block text-sm font-medium text-gray-700">
                                Email
                            </label>
                            <input
                                type="email"
                                id="email"
                                value={data.email}
                                onChange={e => setData('email', e.target.value)}
                                className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 ${
                                    errors.email ? 'border-red-500' : ''
                                }`}
                                required
                                autoComplete="email"
                            />
                            {errors.email && (
                                <p className="mt-1 text-sm text-red-600">{errors.email}</p>
                            )}
                        </div>

                        {/* Password */}
                        <div>
                            <label htmlFor="password" className="block text-sm font-medium text-gray-700">
                                Mot de passe
                            </label>
                            <input
                                type="password"
                                id="password"
                                value={data.password}
                                onChange={e => setData('password', e.target.value)}
                                className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 ${
                                    errors.password ? 'border-red-500' : ''
                                }`}
                                required
                                autoComplete="current-password"
                            />
                            {errors.password && (
                                <p className="mt-1 text-sm text-red-600">{errors.password}</p>
                            )}
                        </div>

                        {/* Remember Me */}
                        <div className="flex items-center justify-between">
                            <div className="flex items-center">
                                <input
                                    type="checkbox"
                                    id="remember"
                                    checked={data.remember}
                                    onChange={e => setData('remember', e.target.checked)}
                                    className="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                />
                                <label htmlFor="remember" className="ml-2 block text-sm text-gray-700">
                                    Se souvenir de moi
                                </label>
                            </div>

                            {canResetPassword && (
                                <Link
                                    href={route('password.request')}
                                    className="text-sm text-emerald-600 hover:text-emerald-500"
                                >
                                    Mot de passe oublié ?
                                </Link>
                            )}
                        </div>

                        <div>
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? 'Connexion en cours...' : 'Se connecter →'}
                            </button>
                        </div>

                        {/* Register Link */}
                        <div className="text-center mt-6">
                            <p className="text-sm text-gray-600">
                                Pas encore de compte ?{' '}
                                <Link
                                    href={route('register')}
                                    className="font-medium text-emerald-600 hover:text-emerald-500"
                                >
                                    Créer un compte
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}
