import { Link, useForm } from '@inertiajs/react';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        firstname: '',
        email: '',
        password: '',
        password_confirmation: '',
        company_name: '',
        ape_code: '',
        terms: false,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('register'), {
            onFinish: () => {
                setData('password', '');
                setData('password_confirmation', '');
            },
        });
    };

    return (
        <div className="min-h-screen flex">
            {/* Left Side - Green Background with Text */}
            <div className="hidden lg:flex lg:w-1/2 bg-emerald-400 p-12 flex-col justify-between">
                <div>
                    <h1 className="text-white text-4xl font-bold mb-6">Bienvenue parmi nous !</h1>
                    <p className="text-white text-lg mb-8">
                        Inscrivez-vous et partagez l'ensemble de vos produits sur cette plateforme pour renouveler vos annonces.
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

            {/* Right Side - Registration Form */}
            <div className="w-full lg:w-1/2 bg-white p-8 lg:p-12">
                <div className="max-w-md mx-auto">
                    <h2 className="text-2xl font-bold text-gray-900 mb-8">Créer un compte</h2>
                    <p className="text-gray-600 mb-8">Inscrivez-vous et partagez tout vos produits.</p>

                    <form onSubmit={submit} className="space-y-6">
                        <div className="grid grid-cols-2 gap-4">
                            {/* Nom */}
                            <div>
                                <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                                    Nom
                                </label>
                                <input
                                    type="text"
                        id="name"
                        value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 ${
                                        errors.name ? 'border-red-500' : ''
                                    }`}
                                    required
                                />
                                {errors.name && (
                                    <p className="mt-1 text-sm text-red-600">{errors.name}</p>
                                )}
                            </div>

                            {/* Prénom */}
                            <div>
                                <label htmlFor="firstname" className="block text-sm font-medium text-gray-700">
                                    Prénom
                                </label>
                                <input
                                    type="text"
                                    id="firstname"
                                    value={data.firstname}
                                    onChange={e => setData('firstname', e.target.value)}
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 ${
                                        errors.firstname ? 'border-red-500' : ''
                                    }`}
                        required
                    />
                                {errors.firstname && (
                                    <p className="mt-1 text-sm text-red-600">{errors.firstname}</p>
                                )}
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            {/* Nom d'entreprise */}
                            <div>
                                <label htmlFor="company_name" className="block text-sm font-medium text-gray-700">
                                    Nom d'entreprise
                                </label>
                                <input
                                    type="text"
                                    id="company_name"
                                    value={data.company_name}
                                    onChange={e => setData('company_name', e.target.value)}
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 ${
                                        errors.company_name ? 'border-red-500' : ''
                                    }`}
                                />
                                {errors.company_name && (
                                    <p className="mt-1 text-sm text-red-600">{errors.company_name}</p>
                                )}
                </div>

                            {/* Code APE */}
                            <div>
                                <label htmlFor="ape_code" className="block text-sm font-medium text-gray-700">
                                    Code APE
                                </label>
                                <input
                                    type="text"
                                    id="ape_code"
                                    value={data.ape_code}
                                    onChange={e => setData('ape_code', e.target.value)}
                                    placeholder="AAAABBDDDF"
                                    className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 ${
                                        errors.ape_code ? 'border-red-500' : ''
                                    }`}
                                />
                                {errors.ape_code && (
                                    <p className="mt-1 text-sm text-red-600">{errors.ape_code}</p>
                                )}
                            </div>
                        </div>

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
                    />
                            {errors.email && (
                                <p className="mt-1 text-sm text-red-600">{errors.email}</p>
                            )}
                </div>

                        {/* Mot de passe */}
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
                    />
                            {errors.password && (
                                <p className="mt-1 text-sm text-red-600">{errors.password}</p>
                            )}
                </div>

                        {/* Confirmation du mot de passe */}
                        <div>
                            <label htmlFor="password_confirmation" className="block text-sm font-medium text-gray-700">
                                Confirmation du mot de passe
                            </label>
                            <input
                                type="password"
                        id="password_confirmation"
                        value={data.password_confirmation}
                                onChange={e => setData('password_confirmation', e.target.value)}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        required
                    />
                        </div>

                        {/* Terms */}
                        <div className="flex items-start">
                            <div className="flex items-center h-5">
                                <input
                                    type="checkbox"
                                    id="terms"
                                    checked={data.terms}
                                    onChange={e => setData('terms', e.target.checked)}
                                    className={`h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 ${
                                        errors.terms ? 'border-red-500' : ''
                                    }`}
                                    required
                                />
                            </div>
                            <div className="ml-3 text-sm">
                                <label htmlFor="terms" className="font-medium text-gray-700">
                                    En cliquant sur créer un compte vous acceptez nos{' '}
                                    <a href="#" className="text-emerald-600 hover:text-emerald-500">
                                        conditions générales
                                    </a>{' '}
                                    et notre{' '}
                                    <a href="#" className="text-emerald-600 hover:text-emerald-500">
                                        politique de confidentialité
                                    </a>
                                </label>
                                {errors.terms && (
                                    <p className="mt-1 text-sm text-red-600">{errors.terms}</p>
                                )}
                            </div>
                        </div>

                        <div>
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black"
                            >
                                Je créé mon compte →
                            </button>
                </div>

                        {/* Login Link */}
                        <div className="text-center mt-6">
                            <p className="text-sm text-gray-600">
                                Déjà un compte ?{' '}
                    <Link
                        href={route('login')}
                                    className="font-medium text-emerald-600 hover:text-emerald-500"
                    >
                                    Se connecter
                    </Link>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}
