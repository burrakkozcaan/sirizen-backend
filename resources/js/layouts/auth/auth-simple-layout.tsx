import AppLogoIcon from '@/components/app-logo-icon';
import { home } from '@/routes';
import { Link } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

interface AuthLayoutProps {
    name?: string;
    title?: string;
    description?: string;
}

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: PropsWithChildren<AuthLayoutProps>) {
    return (
        <div className="flex min-h-svh flex-col items-center justify-center bg-white dark:bg-zinc-950 p-4 md:p-10 overflow-visible">
            <div className="w-full max-w-md overflow-visible">
                <div className="flex flex-col gap-6 overflow-visible">
                    {/* Logo */}
                    <div className="flex flex-col items-center gap-3">
                        <Link
                            href={home()}
                            className="flex flex-col items-center gap-2 font-medium"
                        >
                            <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-sm border border-zinc-950/10 dark:border-white/10 dark:bg-zinc-900">
                                <AppLogoIcon className="size-8" />
                            </div>
                        </Link>
                        <div className="space-y-1 text-center">
                            <h1 className="text-lg font-semibold tracking-tight">{title}</h1>
                            <p className="text-center text-sm text-muted-foreground">
                                {description}
                            </p>
                        </div>
                    </div>

                    {/* Card */}
                    <div className="rounded-2xl border border-zinc-950/10 bg-white px-6 py-8 dark:border-white/10 dark:bg-zinc-900">
                        {children}
                    </div>
                </div>
            </div>
        </div>
    );
}
