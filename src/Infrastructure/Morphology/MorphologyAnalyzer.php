<?php

namespace app\Infrastructure\Morphology;

/**
 * Упрощённый морфологический анализатор для русского языка
 * В production рекомендуется использовать phpmorphy или аналог
 */
class MorphologyAnalyzer
{
    /**
     * Получить нормализованные формы слова
     */
    public function getNormalizedForms(string $word): array
    {
        $word = mb_strtolower(trim($word), 'UTF-8');

        // Если фраза из нескольких слов
        if (str_contains($word, ' ')) {
            return $this->getNormalizedFormsForPhrase($word);
        }

        $forms = [$word]; // Оригинальная форма

        // Добавляем stem (основу слова)
        $stem = $this->stem($word);
        if ($stem !== $word) {
            $forms[] = $stem;
        }

        return array_unique($forms);
    }

    /**
     * Нормализация фразы
     */
    private function getNormalizedFormsForPhrase(string $phrase): array
    {
        $words = explode(' ', $phrase);
        $normalizedWords = [];

        foreach ($words as $word) {
            $normalizedWords[] = $this->stem($word);
        }

        return [
            $phrase, // оригинальная фраза
            implode(' ', $normalizedWords), // нормализованная фраза
        ];
    }

    /**
     * Примитивная стеммизация для русского языка
     * Удаляет распространённые окончания
     */
    private function stem(string $word): string
    {
        $word = mb_strtolower($word, 'UTF-8');
        $length = mb_strlen($word, 'UTF-8');

        if ($length < 4) {
            return $word;
        }

        // Удаляем распространённые окончания
        $endings = [
            'ость', 'ости', 'остей', 'остью', 'остям', 'остями', 'остях',
            'ение', 'ения', 'ению', 'ением', 'ениях', 'ениям', 'ениями',
            'ание', 'ания', 'анию', 'анием', 'аниях', 'аниям', 'аниями',
            'ство', 'ства', 'ству', 'ством', 'ствах', 'ствам', 'ствами',
            'ение', 'ений', 'ениям', 'ениях',
            'ами', 'ием', 'ием', 'иям', 'иях',
            'ов', 'ев', 'ам', 'ям', 'ах', 'ях',
            'ий', 'ая', 'ое', 'ие', 'ого', 'его', 'ому', 'ему',
            'ым', 'им', 'ой', 'ей', 'ую', 'юю', 'ою', 'ею',
            'ыми', 'ими', 'ых', 'их',
            'ть', 'чь', 'ти', 'ешь', 'ишь', 'ете', 'ите',
            'ут', 'ют', 'ат', 'ят', 'ла', 'ло', 'ли',
            'ал', 'ил', 'ел', 'ала', 'ила', 'ела',
            'ало', 'ило', 'ело', 'али', 'или', 'ели',
            'ы', 'и', 'а', 'о', 'у', 'е', 'й', 'я', 'ю',
        ];

        // Сортируем окончания по длине (сначала длинные)
        usort($endings, function($a, $b) {
            return mb_strlen($b, 'UTF-8') - mb_strlen($a, 'UTF-8');
        });

        foreach ($endings as $ending) {
            $endingLen = mb_strlen($ending, 'UTF-8');
            if ($length > $endingLen + 2) { // Оставляем минимум 3 символа
                $wordEnding = mb_substr($word, -$endingLen, null, 'UTF-8');
                if ($wordEnding === $ending) {
                    return mb_substr($word, 0, $length - $endingLen, 'UTF-8');
                }
            }
        }

        return $word;
    }

    /**
     * Проверка вхождения ключевого слова в текст с учётом морфологии
     */
    public function matchInText(array $normalizedForms, string $text): bool
    {
        $text = mb_strtolower($text, 'UTF-8');

        foreach ($normalizedForms as $form) {
            if (mb_strpos($text, $form) !== false) {
                return true;
            }
        }

        return false;
    }
}
