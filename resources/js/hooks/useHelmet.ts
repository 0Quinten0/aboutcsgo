import { helmetContext } from "../contexts/HelmetContext";
import { useContext } from "react";

type helmetContextType = {
    title: string;
    setTitle: (title: string) => void;
    meta: any;
    setMeta: (meta: any) => void;
    link: any;
    setLink: (link: any) => void;
    script: any;
    setScript: (script: any) => void;
    style: string | [];
    setStyle: (style: string | []) => void;
};

const useHelmet = (): helmetContextType => {
    return useContext(helmetContext) as helmetContextType;
};

export default useHelmet;
