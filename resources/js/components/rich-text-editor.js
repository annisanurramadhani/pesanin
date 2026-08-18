import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import { TextStyle } from "@tiptap/extension-text-style";
import { Color } from "@tiptap/extension-color";
import { Highlight } from "@tiptap/extension-highlight";

export function createRichTextEditor({
    element,
    content = "",
    onUpdate = null,
}) {
    const editor = new Editor({
        element,

        extensions: [
            StarterKit,

            TextStyle,

            Color.configure({
                types: ["textStyle"],
            }),

            Highlight.configure({
                multicolor: true,
            }),
        ],

        content,

        editorProps: {
            attributes: {
                class: "min-h-[180px] outline-none",
            },
        },

        onUpdate: ({ editor }) => {
            if (onUpdate) {
                onUpdate(editor.getHTML());
            }
        },
    });

    return editor;
}