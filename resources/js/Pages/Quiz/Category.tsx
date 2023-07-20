import { useEffect, useState } from "react";
import { Question } from "../../types/quiz";

type IProps = {
    question: Question;
}
export default function Category(props: IProps) {
    const [parts, setParts] = useState<string[]>([]);
    useEffect(() => {
        const parts = [];
        if (typeof props.question.category.parent !== "undefined") {
            parts.push(props.question.category.parent.category_name);
        }
        parts.push(props.question.category.category_name);
        // Add Tags
        props.question.tags.forEach(tag => {
            parts.push(tag.tag_name);
        });
        setParts(parts);
    }, [props.question])

    return (
        <div className="text-sm breadcrumbs text-gray-200">
            <ul>
                {
                    parts.map((part, i) => (
                        <li key={i}>
                            {part}
                        </li>
                    ))
                }
            </ul>
        </div>

    )
}
