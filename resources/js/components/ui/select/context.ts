export type SelectContext = {
    open: () => boolean;
    setOpen: (value: boolean) => void;
    value: () => string;
    setValue: (value: string) => void;
};

export const SELECT_CONTEXT = Symbol('select');
