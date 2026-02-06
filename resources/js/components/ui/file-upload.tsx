'use client';

import * as React from 'react';
import { Upload, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

interface FileUploadContextValue {
    maxFiles?: number;
    maxSize?: number;
    value: File[];
    onValueChange: (files: File[]) => void;
    onFileReject?: (file: File, message: string) => void;
    multiple?: boolean;
    handleFileSelect: (files: FileList | null) => void;
}

const FileUploadContext = React.createContext<FileUploadContextValue | null>(
    null,
);

function useFileUpload() {
    const context = React.useContext(FileUploadContext);
    if (!context) {
        throw new Error('useFileUpload must be used within FileUpload');
    }
    return context;
}

interface FileUploadProps extends React.HTMLAttributes<HTMLDivElement> {
    maxFiles?: number;
    maxSize?: number;
    value: File[];
    onValueChange: (files: File[]) => void;
    onFileReject?: (file: File, message: string) => void;
    multiple?: boolean;
    children: React.ReactNode;
}

function FileUpload({
    maxFiles,
    maxSize = 5 * 1024 * 1024, // 5MB default
    value,
    onValueChange,
    onFileReject,
    multiple = false,
    className,
    children,
    ...props
}: FileUploadProps) {
    const handleFileSelect = React.useCallback(
        (files: FileList | null) => {
            if (!files) return;

            const fileArray = Array.from(files);
            const validFiles: File[] = [];
            const rejectedFiles: { file: File; reason: string }[] = [];

            fileArray.forEach((file) => {
                // Check file size
                if (file.size > maxSize) {
                    rejectedFiles.push({
                        file,
                        reason: `Dosya boyutu çok büyük (max ${(maxSize / 1024 / 1024).toFixed(0)}MB)`,
                    });
                    return;
                }

                // Check max files
                if (maxFiles && value.length + validFiles.length >= maxFiles) {
                    rejectedFiles.push({
                        file,
                        reason: `Maksimum ${maxFiles} dosya seçebilirsiniz`,
                    });
                    return;
                }

                validFiles.push(file);
            });

            // Call reject callback for rejected files
            rejectedFiles.forEach(({ file, reason }) => {
                onFileReject?.(file, reason);
            });

            // Update files
            if (validFiles.length > 0) {
                const newFiles = multiple
                    ? [...value, ...validFiles]
                    : validFiles.slice(0, 1);
                onValueChange(newFiles);
            }
        },
        [maxFiles, maxSize, value, onValueChange, onFileReject, multiple],
    );

    return (
        <FileUploadContext.Provider
            value={{
                maxFiles,
                maxSize,
                value,
                onValueChange,
                onFileReject,
                multiple,
                handleFileSelect,
            }}
        >
            <div className={cn('w-full', className)} {...props}>
                {children}
            </div>
        </FileUploadContext.Provider>
    );
}

interface FileUploadDropzoneProps
    extends React.HTMLAttributes<HTMLDivElement> {
    children: React.ReactNode;
}

function FileUploadDropzone({
    className,
    children,
    ...props
}: FileUploadDropzoneProps) {
    const { handleFileSelect } = useFileUpload();
    const [isDragging, setIsDragging] = React.useState(false);
    const fileInputRef = React.useRef<HTMLInputElement>(null);

    const handleDragOver = React.useCallback((e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(true);
    }, []);

    const handleDragLeave = React.useCallback((e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);
    }, []);

    const handleDrop = React.useCallback(
        (e: React.DragEvent) => {
            e.preventDefault();
            e.stopPropagation();
            setIsDragging(false);
            handleFileSelect(e.dataTransfer.files);
        },
        [handleFileSelect],
    );

    const handleClick = React.useCallback(
        (e: React.MouseEvent) => {
            // Don't trigger if clicking on button inside
            if (
                (e.target as HTMLElement).closest('button') ||
                (e.target as HTMLElement).tagName === 'BUTTON'
            ) {
                return;
            }
            fileInputRef.current?.click();
        },
        [],
    );

    return (
        <div
            className={cn(
                'relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed p-8 transition-colors',
                isDragging
                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/20'
                    : 'border-gray-300 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-600',
                className,
            )}
            onDragOver={handleDragOver}
            onDragLeave={handleDragLeave}
            onDrop={handleDrop}
            onClick={handleClick}
            {...props}
        >
            <input
                ref={fileInputRef}
                type="file"
                multiple
                accept="image/*"
                className="hidden"
                onChange={(e) => {
                    handleFileSelect(e.target.files);
                    e.target.value = ''; // Reset input
                }}
            />
            {children}
        </div>
    );
}

function FileUploadTrigger({
    asChild,
    children,
    ...props
}: React.ButtonHTMLAttributes<HTMLButtonElement> & {
    asChild?: boolean;
    children: React.ReactNode;
}) {
    const { handleFileSelect } = useFileUpload();
    const fileInputRef = React.useRef<HTMLInputElement>(null);

    const handleClick = React.useCallback(() => {
        fileInputRef.current?.click();
    }, []);

    if (asChild && React.isValidElement(children)) {
        return (
            <>
                <input
                    ref={fileInputRef}
                    type="file"
                    multiple
                    accept="image/*"
                    className="hidden"
                    onChange={(e) => {
                        handleFileSelect(e.target.files);
                        e.target.value = '';
                    }}
                />
                {React.cloneElement(children, {
                    ...props,
                    onClick: (e: React.MouseEvent) => {
                        handleClick();
                        children.props.onClick?.(e);
                    },
                } as any)}
            </>
        );
    }

    return (
        <>
            <input
                ref={fileInputRef}
                type="file"
                multiple
                accept="image/*"
                className="hidden"
                onChange={(e) => {
                    handleFileSelect(e.target.files);
                    e.target.value = '';
                }}
            />
            <button type="button" onClick={handleClick} {...props}>
                {children}
            </button>
        </>
    );
}

function FileUploadList({
    className,
    children,
    ...props
}: React.HTMLAttributes<HTMLDivElement>) {
    return (
        <div
            className={cn('mt-4 grid grid-cols-2 gap-4 md:grid-cols-4', className)}
            {...props}
        >
            {children}
        </div>
    );
}

function FileUploadItem({
    value,
    className,
    children,
    ...props
}: React.HTMLAttributes<HTMLDivElement> & {
    value: File;
    children: React.ReactNode;
}) {
    const [preview, setPreview] = React.useState<string | null>(null);

    React.useEffect(() => {
        if (value && value.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onloadend = () => {
                setPreview(reader.result as string);
            };
            reader.readAsDataURL(value);
        }

        return () => {
            if (preview && preview.startsWith('blob:')) {
                URL.revokeObjectURL(preview);
            }
        };
    }, [value]);

    return (
        <FileUploadItemContext.Provider value={{ file: value }}>
            <div
                className={cn(
                    'relative group aspect-square rounded-lg border overflow-hidden bg-gray-50 dark:bg-gray-900',
                    className,
                )}
                {...props}
            >
                {preview && (
                    <img
                        src={preview}
                        alt={value.name}
                        className="h-full w-full object-cover"
                    />
                )}
                {children}
            </div>
        </FileUploadItemContext.Provider>
    );
}

function FileUploadItemPreview({
    className,
    ...props
}: React.ImgHTMLAttributes<HTMLImageElement>) {
    // Rendered by FileUploadItem
    return null;
}

function FileUploadItemMetadata({
    className,
    children,
    ...props
}: React.HTMLAttributes<HTMLDivElement>) {
    return (
        <div
            className={cn(
                'absolute bottom-0 left-0 right-0 bg-black/60 p-2 text-white text-xs',
                className,
            )}
            {...props}
        >
            {children}
        </div>
    );
}

function FileUploadItemDelete({
    asChild,
    children,
    ...props
}: React.ButtonHTMLAttributes<HTMLButtonElement> & {
    asChild?: boolean;
    children?: React.ReactNode;
}) {
    const { value, onValueChange } = useFileUpload();
    const { file } = useFileUploadItem();

    const handleDelete = React.useCallback(
        (e: React.MouseEvent) => {
            e.stopPropagation();
            const newFiles = value.filter((f) => f !== file);
            onValueChange(newFiles);
        },
        [file, value, onValueChange],
    );

    if (asChild && React.isValidElement(children)) {
        return React.cloneElement(children, {
            ...props,
            onClick: (e: React.MouseEvent) => {
                handleDelete(e);
                children.props.onClick?.(e);
            },
        } as any);
    }

    return (
        <button
            type="button"
            onClick={handleDelete}
            className={cn(
                'absolute top-2 right-2 rounded-full bg-red-500 p-1.5 text-white opacity-0 transition-opacity hover:bg-red-600 group-hover:opacity-100',
                props.className,
            )}
            {...props}
        >
            {children || <X className="h-3 w-3" />}
        </button>
    );
}

const FileUploadItemContext = React.createContext<{ file: File } | null>(
    null,
);

function useFileUploadItem() {
    const context = React.useContext(FileUploadItemContext);
    if (!context) {
        throw new Error(
            'useFileUploadItem must be used within FileUploadItem',
        );
    }
    return context;
}

export {
    FileUpload,
    FileUploadDropzone,
    FileUploadTrigger,
    FileUploadList,
    FileUploadItem,
    FileUploadItemPreview,
    FileUploadItemMetadata,
    FileUploadItemDelete,
};
