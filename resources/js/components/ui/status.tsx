import * as React from 'react';
import { cn } from '@/lib/utils';

interface StatusProps extends React.HTMLAttributes<HTMLDivElement> {
    variant?: 'success' | 'error' | 'warning' | 'info' | 'default';
    children: React.ReactNode;
}

function Status({
    variant = 'default',
    className,
    children,
    ...props
}: StatusProps) {
    return (
        <div
            className={cn(
                'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium',
                {
                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400':
                        variant === 'success',
                    'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400':
                        variant === 'error',
                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-950 dark:text-yellow-400':
                        variant === 'warning',
                    'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400':
                        variant === 'info',
                    'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300':
                        variant === 'default',
                },
                className,
            )}
            {...props}
        >
            {children}
        </div>
    );
}

function StatusIndicator({
    variant,
    className,
    ...props
}: React.HTMLAttributes<HTMLSpanElement> & {
    variant?: 'success' | 'error' | 'warning' | 'info' | 'default';
}) {
    return (
        <span
            className={cn(
                'h-1.5 w-1.5 rounded-full',
                {
                    'bg-emerald-500': variant === 'success',
                    'bg-red-500': variant === 'error',
                    'bg-yellow-500': variant === 'warning',
                    'bg-blue-500': variant === 'info',
                    'bg-gray-500': variant === 'default',
                },
                className,
            )}
            {...props}
        />
    );
}

function StatusLabel({
    className,
    children,
    ...props
}: React.HTMLAttributes<HTMLSpanElement>) {
    return (
        <span className={cn('', className)} {...props}>
            {children}
        </span>
    );
}

export { Status, StatusIndicator, StatusLabel };

