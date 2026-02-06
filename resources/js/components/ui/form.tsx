import * as React from 'react';
import { useFormContext, Controller, type FieldPath, type FieldValues } from 'react-hook-form';
import { cn } from '@/lib/utils';

const Form = React.forwardRef<
    HTMLFormElement,
    React.FormHTMLAttributes<HTMLFormElement> & {
        children: React.ReactNode;
    }
>(({ children, ...props }, ref) => {
    return (
        <form ref={ref} {...props}>
            {children}
        </form>
    );
});
Form.displayName = 'Form';

const FormField = <
    TFieldValues extends FieldValues = FieldValues,
    TName extends FieldPath<TFieldValues> = FieldPath<TFieldValues>,
>({
    control,
    name,
    render,
}: {
    control?: any;
    name: TName;
    render: (props: {
        field: {
            value: any;
            onChange: (value: any) => void;
            onBlur: () => void;
        };
        fieldState: {
            error?: { message?: string };
        };
    }) => React.ReactNode;
}) => {
    const formContext = useFormContext<TFieldValues>();
    const actualControl = control || formContext.control;

    return (
        <Controller
            control={actualControl}
            name={name}
            render={({ field, fieldState }) => render({ field, fieldState })}
        />
    );
};

const FormItem = React.forwardRef<
    HTMLDivElement,
    React.HTMLAttributes<HTMLDivElement>
>(({ className, ...props }, ref) => {
    return (
        <div ref={ref} className={cn('space-y-2', className)} {...props} />
    );
});
FormItem.displayName = 'FormItem';

const FormLabel = React.forwardRef<
    HTMLLabelElement,
    React.LabelHTMLAttributes<HTMLLabelElement>
>(({ className, ...props }, ref) => {
    return (
        <label
            ref={ref}
            className={cn(
                'text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70',
                className,
            )}
            {...props}
        />
    );
});
FormLabel.displayName = 'FormLabel';

const FormControl = React.forwardRef<
    HTMLDivElement,
    React.HTMLAttributes<HTMLDivElement>
>(({ ...props }, ref) => {
    return <div ref={ref} {...props} />;
});
FormControl.displayName = 'FormControl';

const FormDescription = React.forwardRef<
    HTMLParagraphElement,
    React.HTMLAttributes<HTMLParagraphElement>
>(({ className, ...props }, ref) => {
    return (
        <p
            ref={ref}
            className={cn('text-sm text-muted-foreground', className)}
            {...props}
        />
    );
});
FormDescription.displayName = 'FormDescription';

const FormMessage = ({
    className,
    children,
    ...props
}: React.HTMLAttributes<HTMLParagraphElement> & {
    children?: React.ReactNode;
}) => {
    if (!children) {
        return null;
    }

    return (
        <p
            className={cn('text-sm font-medium text-red-500', className)}
            {...props}
        >
            {children}
        </p>
    );
};
FormMessage.displayName = 'FormMessage';

export {
    Form,
    FormField,
    FormItem,
    FormLabel,
    FormControl,
    FormDescription,
    FormMessage,
};

