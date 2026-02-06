import { Head, Link } from '@inertiajs/react';
import AuthLayout from '@/layouts/auth-layout';
import { Button } from '@/components/ui/button';
import { login } from '@/routes';

export default function VendorApplicationPending() {
    return (
        <AuthLayout
            title="Başvurunuz değerlendiriliyor"
            description="Başvurunuz incelenecek ve onaylandıktan sonra giriş yapabileceksiniz."
        >
            <Head title="Başvuru Alındı" />
            <div className="flex flex-col gap-6">
                <p className="text-sm text-muted-foreground">
                    Başvurunuz değerlendirmeye alındı. Onaylandığında e-posta
                    adresinize bilgilendirme gönderilecektir.
                </p>
                <Button asChild className="w-full">
                    <Link href={login()}>Giriş sayfasına git</Link>
                </Button>
            </div>
        </AuthLayout>
    );
}
