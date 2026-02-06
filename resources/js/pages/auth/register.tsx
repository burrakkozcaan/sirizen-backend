import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { PasswordInput } from '@/components/ui/password-input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';

type CategoryOption = {
    id: number;
    name: string;
};

interface RegisterProps {
    categories?: CategoryOption[];
}


const companyTypes = [
    { value: 'sahis', label: 'Şahıs Şirketi' },
    { value: 'limited', label: 'Limited Şirket' },
    { value: 'anonim', label: 'Anonim Şirket' },
    { value: 'kooperatif', label: 'Kooperatif' },
    { value: 'kolektif', label: 'Kolektif Şirket' },
    { value: 'komandit', label: 'Komandit Şirket' },
    { value: 'diger', label: 'Diğer' },
];

const districtsByCity: Record<string, string[]> = {
    İstanbul: ['Beşiktaş', 'Kadıköy', 'Bakırköy', 'Üsküdar', 'Ataşehir'],
    Ankara: ['Çankaya', 'Keçiören', 'Yenimahalle', 'Etimesgut', 'Mamak'],
    İzmir: ['Konak', 'Bornova', 'Karşıyaka', 'Buca', 'Bayraklı'],
};

export default function Register({ categories = [] }: RegisterProps) {
    const [categoryId, setCategoryId] = useState('');
    const [companyType, setCompanyType] = useState('');
    const [city, setCity] = useState('');
    const [district, setDistrict] = useState('');

    const districtOptions = useMemo(() => {
        if (!city) {
            return [];
        }

        return districtsByCity[city] ?? [];
    }, [city]);

    return (
        <AuthLayout
            title="Sirizen Pazaryeri"
            description="Satıcı başvurusu için bilgilerinizi doldurun"
        >
            <Head title="Sistem Pazaryeri Başvurusu" />
            <Form
                {...store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6 overflow-visible"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6 overflow-visible">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Şirket ismi</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="name"
                                    name="name"
                                    placeholder="Şirket İsmi"
                                />
                                <InputError
                                    message={errors.name}
                                    className="mt-2"
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="phone">Cep telefonunuz</Label>
                                <Input
                                    id="phone"
                                    type="tel"
                                    required
                                    tabIndex={2}
                                    autoComplete="tel"
                                    name="phone"
                                    placeholder="05__ ___ __ __"
                                />
                                <InputError message={errors.phone} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">E-posta adresiniz</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabIndex={3}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="E-Posta Adresi"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="category_id">
                                    Satılacak ürün kategorisi
                                </Label>
                                <Select
                                    value={categoryId}
                                    onValueChange={setCategoryId}
                                >
                                    <SelectTrigger id="category_id" tabIndex={4}>
                                        <SelectValue placeholder="Seçim yapınız" />
                                    </SelectTrigger>
                                    <SelectContent className="z-[100]">
                                        {categories.map((category) => (
                                            <SelectItem
                                                key={category.id}
                                                value={String(category.id)}
                                            >
                                                {category.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <input
                                    type="hidden"
                                    name="category_id"
                                    value={categoryId}
                                />
                                <p className="text-xs text-muted-foreground">
                                    Kategori bilgisi zorunlu bir alandır.
                                </p>
                                <InputError message={errors.category_id} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="company_type">
                                    Şirket türü
                                </Label>
                                <Select
                                    value={companyType}
                                    onValueChange={setCompanyType}
                                >
                                    <SelectTrigger
                                        id="company_type"
                                        tabIndex={5}
                                    >
                                        <SelectValue placeholder="Seçim yapınız" />
                                    </SelectTrigger>
                                    <SelectContent className="z-[100]">
                                {companyTypes.map((option) => (
                                    <SelectItem
                                        key={option.value}
                                        value={option.value}
                                    >
                                        {option.label}
                                    </SelectItem>
                                ))}
                                    </SelectContent>
                                </Select>
                                <input
                                    type="hidden"
                                    name="company_type"
                                    value={companyType}
                                />
                                <InputError message={errors.company_type} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="tax_number">
                                    Vergi kimlik numaranız
                                </Label>
                                <Input
                                    id="tax_number"
                                    type="text"
                                    required
                                    tabIndex={6}
                                    autoComplete="off"
                                    name="tax_number"
                                    placeholder="Vergi Kimlik Numaranız"
                                />
                                <InputError message={errors.tax_number} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="city">İl</Label>
                                <Select
                                    value={city}
                                    onValueChange={(value) => {
                                        setCity(value);
                                        setDistrict('');
                                    }}
                                >
                                    <SelectTrigger id="city" tabIndex={7}>
                                        <SelectValue placeholder="Seçim yapınız" />
                                    </SelectTrigger>
                                    <SelectContent className="z-[100]">
                                        {Object.keys(districtsByCity).map(
                                            (option) => (
                                                <SelectItem
                                                    key={option}
                                                    value={option}
                                                >
                                                    {option}
                                                </SelectItem>
                                            ),
                                        )}
                                    </SelectContent>
                                </Select>
                                <input type="hidden" name="city" value={city} />
                                <InputError message={errors.city} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="district">İlçe</Label>
                                <Select
                                    value={district}
                                    onValueChange={setDistrict}
                                    disabled={!city}
                                >
                                    <SelectTrigger id="district" tabIndex={8}>
                                        <SelectValue placeholder="Seçim yapınız" />
                                    </SelectTrigger>
                                    <SelectContent className="z-[100]">
                                        {districtOptions.map((option) => (
                                            <SelectItem
                                                key={option}
                                                value={option}
                                            >
                                                {option}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <input
                                    type="hidden"
                                    name="district"
                                    value={district}
                                />
                                <InputError message={errors.district} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="business_license_number">
                                    İşletme ruhsat numarası
                                </Label>
                                <Input
                                    id="business_license_number"
                                    type="text"
                                    tabIndex={9}
                                    autoComplete="off"
                                    name="business_license_number"
                                    placeholder="İşletme Ruhsat Numaranız"
                                />
                                <InputError message={errors.business_license_number} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="iban">IBAN</Label>
                                <Input
                                    id="iban"
                                    type="text"
                                    tabIndex={10}
                                    autoComplete="off"
                                    name="iban"
                                    placeholder="TR00 0000 0000 0000 0000 0000 00"
                                />
                                <InputError message={errors.iban} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="bank_name">Banka adı</Label>
                                <Input
                                    id="bank_name"
                                    type="text"
                                    tabIndex={11}
                                    autoComplete="off"
                                    name="bank_name"
                                    placeholder="Banka Adı"
                                />
                                <InputError message={errors.bank_name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="account_holder_name">
                                    Hesap sahibi adı
                                </Label>
                                <Input
                                    id="account_holder_name"
                                    type="text"
                                    tabIndex={12}
                                    autoComplete="off"
                                    name="account_holder_name"
                                    placeholder="Hesap Sahibi Adı"
                                />
                                <InputError message={errors.account_holder_name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="address">Adres</Label>
                                <textarea
                                    id="address"
                                    tabIndex={13}
                                    autoComplete="off"
                                    name="address"
                                    placeholder="Tam Adres Bilgisi"
                                    rows={3}
                                    className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                />
                                <InputError message={errors.address} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="reference_code">
                                    Referans kodu (zorunlu değil)
                                </Label>
                                <Input
                                    id="reference_code"
                                    type="text"
                                    tabIndex={14}
                                    autoComplete="off"
                                    name="reference_code"
                                    placeholder="Varsa Referans Kodu Giriniz..."
                                />
                                <InputError message={errors.reference_code} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Şifre</Label>
                                <PasswordInput
                                    id="password"
                                    required
                                    tabIndex={15}
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="Şifre"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">
                                    Şifreyi doğrula
                                </Label>
                                <PasswordInput
                                    id="password_confirmation"
                                    required
                                    tabIndex={16}
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Şifreyi tekrar girin"
                                />
                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            <Button
                                type="submit"
                                className="mt-2 w-full"
                                tabIndex={17}
                                data-test="register-user-button"
                            >
                                {processing && <Spinner />}
                                Başvuruyu gönder
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Zaten hesabınız var mı?{' '}
                            <TextLink href={login()} tabIndex={18}>
                                Giriş yap
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
