export type Questions = Question[];

export type Question = {
    answers: Answer[];
    category: Category;
    category_id: number;
    detail_url: null|string;
    hint: string;
    id: number;
    question: string;
    question_type: QuestionType;
    question_type_id: number;
    tags: Tag[];
}

export type Answer = {
    answer: string;
    id: number;
    is_correct: 1 | 0;
    question_id: number;
}

export type Category = {
    id: number;
    category_name: string;
    parent?: Category;
    parent_id: null | number;
}

export type QuestionType = {
    id: number;
    question_type: string;
}

export type Tag = {
    id: number;
    tag_name: string;
}
