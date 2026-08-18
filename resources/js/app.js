import Alpine from "alpinejs";
import { createRichTextEditor } from "./components/rich-text-editor";

window.Alpine = Alpine;
window.createRichTextEditor = createRichTextEditor;

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    document
        .querySelectorAll("[data-rich-text-editor]")
        .forEach((wrapper) => {
            const editorElement = wrapper.querySelector(
                "[data-editor-content]"
            );

            const inputName = wrapper.dataset.name;

            const input = document.querySelector(`#${inputName}`);

            if (!editorElement || !input) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Create Editor
            |--------------------------------------------------------------------------
            */

            const editor = createRichTextEditor({
                element: editorElement,

                content: input.value || "",

                onUpdate: (html) => {
                    input.value = html;
                },
            });

            wrapper._tiptapEditor = editor;

            /*
            |--------------------------------------------------------------------------
            | Update Toolbar State
            |--------------------------------------------------------------------------
            */

            const updateToolbarState = () => {
                wrapper
                    .querySelectorAll(
                        '[data-command]:not([data-command="heading"])'
                    )
                    .forEach((button) => {
                        const command = button.dataset.command;

                        let isActive = false;
                        let isDisabled = false;

                        switch (command) {
                            /*
                            |--------------------------------------------------------------------------
                            | Text Formatting
                            |--------------------------------------------------------------------------
                            */

                            case "bold":
                                isActive = editor.isActive("bold");
                                break;

                            case "italic":
                                isActive = editor.isActive("italic");
                                break;

                            case "underline":
                                isActive = editor.isActive("underline");
                                break;

                            case "strike":
                                isActive = editor.isActive("strike");
                                break;

                            /*
                            |--------------------------------------------------------------------------
                            | Lists
                            |--------------------------------------------------------------------------
                            */

                            case "bulletList":
                                isActive = editor.isActive("bulletList");
                                break;

                            case "orderedList":
                                isActive = editor.isActive("orderedList");
                                break;

                            /*
                            |--------------------------------------------------------------------------
                            | Text Color
                            |--------------------------------------------------------------------------
                            */

                            case "textColor":
                                isActive =
                                    editor.getAttributes("textStyle").color !=
                                    null;
                                break;

                            /*
                            |--------------------------------------------------------------------------
                            | Highlight
                            |--------------------------------------------------------------------------
                            */

                            case "highlight":
                                isActive = editor.isActive("highlight");
                                break;

                            /*
                            |--------------------------------------------------------------------------
                            | History
                            |--------------------------------------------------------------------------
                            */

                            case "undo":
                                isDisabled = !editor.can().undo();
                                break;

                            case "redo":
                                isDisabled = !editor.can().redo();
                                break;
                        }

                        button.classList.toggle(
                            "is-active",
                            isActive
                        );

                        button.classList.toggle(
                            "is-disabled",
                            isDisabled
                        );

                        button.disabled = isDisabled;
                    });

                /*
                |--------------------------------------------------------------------------
                | Heading State
                |--------------------------------------------------------------------------
                */

                const headingSelect = wrapper.querySelector(
                    '[data-command="heading"]'
                );

                if (headingSelect) {
                    if (editor.isActive("heading", { level: 1 })) {
                        headingSelect.value = "1";
                    } else if (
                        editor.isActive("heading", { level: 2 })
                    ) {
                        headingSelect.value = "2";
                    } else if (
                        editor.isActive("heading", { level: 3 })
                    ) {
                        headingSelect.value = "3";
                    } else {
                        headingSelect.value = "paragraph";
                    }
                }
            };

            /*
            |--------------------------------------------------------------------------
            | Toolbar Button
            |--------------------------------------------------------------------------
            */

            wrapper
                .querySelectorAll(
                    '[data-command]:not([data-command="heading"])'
                )
                .forEach((button) => {
                    /*
                    |--------------------------------------------------------------------------
                    | Prevent Editor Blur
                    |--------------------------------------------------------------------------
                    */

                    button.addEventListener("mousedown", (event) => {
                        event.preventDefault();
                    });

                    /*
                    |--------------------------------------------------------------------------
                    | Button Click
                    |--------------------------------------------------------------------------
                    */

                    button.addEventListener("click", () => {
                        const command = button.dataset.command;

                        switch (command) {
                            /*
                            |--------------------------------------------------------------------------
                            | Text Formatting
                            |--------------------------------------------------------------------------
                            */

                            case "bold":
                                editor
                                    .chain()
                                    .focus()
                                    .toggleBold()
                                    .run();
                                break;

                            case "italic":
                                editor
                                    .chain()
                                    .focus()
                                    .toggleItalic()
                                    .run();
                                break;

                            case "underline":
                                editor
                                    .chain()
                                    .focus()
                                    .toggleUnderline()
                                    .run();
                                break;

                            case "strike":
                                editor
                                    .chain()
                                    .focus()
                                    .toggleStrike()
                                    .run();
                                break;

                            /*
                            |--------------------------------------------------------------------------
                            | Lists
                            |--------------------------------------------------------------------------
                            */

                            case "bulletList":
                                editor
                                    .chain()
                                    .focus()
                                    .toggleBulletList()
                                    .run();
                                break;

                            case "orderedList":
                                editor
                                    .chain()
                                    .focus()
                                    .toggleOrderedList()
                                    .run();
                                break;

                            /*
                            |--------------------------------------------------------------------------
                            | Text Color
                            |--------------------------------------------------------------------------
                            */

                            case "textColor": {
                                const colorPicker =
                                    wrapper.querySelector(
                                        "[data-color-picker]"
                                    );

                                const highlightPicker =
                                    wrapper.querySelector(
                                        "[data-highlight-picker]"
                                    );

                                /*
                                |----------------------------------------------------------------------
                                | Tutup highlight jika sedang terbuka
                                |----------------------------------------------------------------------
                                */

                                if (highlightPicker) {
                                    highlightPicker.classList.add("hidden");
                                }

                                /*
                                |----------------------------------------------------------------------
                                | Toggle color picker
                                |----------------------------------------------------------------------
                                */

                                if (colorPicker) {
                                    colorPicker.classList.toggle("hidden");
                                }

                                break;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Highlight
                            |--------------------------------------------------------------------------
                            */

                            case "highlight": {
                                const colorPicker =
                                    wrapper.querySelector(
                                        "[data-color-picker]"
                                    );

                                const highlightPicker =
                                    wrapper.querySelector(
                                        "[data-highlight-picker]"
                                    );

                                /*
                                |----------------------------------------------------------------------
                                | Tutup color picker jika sedang terbuka
                                |----------------------------------------------------------------------
                                */

                                if (colorPicker) {
                                    colorPicker.classList.add("hidden");
                                }

                                /*
                                |----------------------------------------------------------------------
                                | Toggle highlight picker
                                |----------------------------------------------------------------------
                                */

                                if (highlightPicker) {
                                    highlightPicker.classList.toggle(
                                        "hidden"
                                    );
                                }

                                break;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | History
                            |--------------------------------------------------------------------------
                            */

                            case "undo":
                                editor
                                    .chain()
                                    .focus()
                                    .undo()
                                    .run();
                                break;

                            case "redo":
                                editor
                                    .chain()
                                    .focus()
                                    .redo()
                                    .run();
                                break;
                        }

                        updateToolbarState();
                    });
                });

            /*
            |--------------------------------------------------------------------------
            | Heading
            |--------------------------------------------------------------------------
            */

            const headingSelect = wrapper.querySelector(
                '[data-command="heading"]'
            );

            if (headingSelect) {
                headingSelect.addEventListener("change", () => {
                    const level = headingSelect.value;

                    if (level === "paragraph") {
                        editor
                            .chain()
                            .focus()
                            .setParagraph()
                            .run();
                    } else {
                        editor
                            .chain()
                            .focus()
                            .toggleHeading({
                                level: Number(level),
                            })
                            .run();
                    }

                    updateToolbarState();
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Text Color Picker
            |--------------------------------------------------------------------------
            */

            const colorPicker = wrapper.querySelector(
                "[data-color-picker]"
            );

            if (colorPicker) {
                colorPicker
                    .querySelectorAll("[data-color]")
                    .forEach((button) => {
                        button.addEventListener(
                            "mousedown",
                            (event) => {
                                event.preventDefault();
                            }
                        );

                        button.addEventListener("click", () => {
                            const color = button.dataset.color;

                            /*
                            |----------------------------------------------------------------------
                            | Reset Text Color
                            |----------------------------------------------------------------------
                            */

                            if (color === "unset") {
                                editor
                                    .chain()
                                    .focus()
                                    .unsetColor()
                                    .run();
                            } else {
                                /*
                                |----------------------------------------------------------------------
                                | Set Text Color
                                |----------------------------------------------------------------------
                                */

                                editor
                                    .chain()
                                    .focus()
                                    .setColor(color)
                                    .run();
                            }

                            colorPicker.classList.add("hidden");

                            updateToolbarState();
                        });
                    });
            }

            /*
            |--------------------------------------------------------------------------
            | Highlight Picker
            |--------------------------------------------------------------------------
            */

            const highlightPicker = wrapper.querySelector(
                "[data-highlight-picker]"
            );

            if (highlightPicker) {
                highlightPicker
                    .querySelectorAll("[data-highlight]")
                    .forEach((button) => {
                        button.addEventListener(
                            "mousedown",
                            (event) => {
                                event.preventDefault();
                            }
                        );

                        button.addEventListener("click", () => {
                            const color = button.dataset.highlight;

                            /*
                            |----------------------------------------------------------------------
                            | Remove Highlight
                            |----------------------------------------------------------------------
                            */

                            if (color === "unset") {
                                editor
                                    .chain()
                                    .focus()
                                    .unsetHighlight()
                                    .run();
                            } else {
                                /*
                                |----------------------------------------------------------------------
                                | Set Highlight
                                |----------------------------------------------------------------------
                                */

                                editor
                                    .chain()
                                    .focus()
                                    .toggleHighlight({
                                        color,
                                    })
                                    .run();
                            }

                            highlightPicker.classList.add(
                                "hidden"
                            );

                            updateToolbarState();
                        });
                    });
            }

            /*
            |--------------------------------------------------------------------------
            | Editor State
            |--------------------------------------------------------------------------
            */

            editor.on(
                "selectionUpdate",
                updateToolbarState
            );

            editor.on(
                "transaction",
                updateToolbarState
            );

            editor.on(
                "focus",
                updateToolbarState
            );

            editor.on(
                "blur",
                updateToolbarState
            );

            /*
            |--------------------------------------------------------------------------
            | Focus Editor
            |--------------------------------------------------------------------------
            */

            editorElement.addEventListener("click", () => {
                editor.commands.focus();
            });

            /*
            |--------------------------------------------------------------------------
            | Initial State
            |--------------------------------------------------------------------------
            */

            updateToolbarState();

            console.log(
                "Tiptap berhasil diinisialisasi:",
                editor
            );
        });
});