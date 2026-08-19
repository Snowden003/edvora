// PHP Coding Hero Animation Script

class PHPCodingAnimation {
    constructor() {
        this.codeLines = [
            '<?php',
            '// Edvora Learning Platform - Main Controller',
            'class LearningPlatform {',
            '    private $students = [];',
            '    private $courses = [];',
            '',
            '    public function enrollStudent($name, $course) {',
            '        $student = new Student($name);',
            '        $student->assignCourse($course);',
            '        return \'Success: Student enrolled!\';',
            '    }',
            '',
            '    public function getProgress($studentId) {',
            '        $progress = $this->calculateProgress($studentId);',
            '        return [',
            '            \'completion\' => $progress,',
            '            \'status\' => \'active\'',
            '        ];',
            '    }',
            '}',
            '?>'
        ];

        this.currentLine = 0;
        this.currentChar = 0;
        this.isTyping = false;
        this.cursor = null;
        this.typingSpeed = 50; // milliseconds per character
        this.lineDelay = 300; // delay between lines
        
        this.init();
    }

    init() {
        this.setupCodeContainer();
        this.createCursor();
        this.startTyping();
        this.setupFloatingElements();
    }

    setupCodeContainer() {
        const codeContent = document.querySelector('.code-content');
        if (!codeContent) return;

        // Clear existing content
        codeContent.innerHTML = '';

        // Create line elements
        this.codeLines.forEach((line, index) => {
            const lineElement = document.createElement('div');
            lineElement.className = 'code-line';
            lineElement.id = `line-${index}`;
            codeContent.appendChild(lineElement);
        });
    }

    createCursor() {
        const codeContent = document.querySelector('.code-content');
        if (!codeContent) return;
        
        this.cursor = document.createElement('div');
        this.cursor.className = 'typing-cursor';
        this.cursor.style.animation = 'typingCursor 1s infinite';
        codeContent.appendChild(this.cursor);
    }

    startTyping() {
        if (this.currentLine >= this.codeLines.length) {
            // Animation complete, restart after delay
            setTimeout(() => {
                this.resetAnimation();
                this.startTyping();
            }, 3000);
            return;
        }

        this.isTyping = true;
        this.typeCurrentLine();
    }

    typeCurrentLine() {
        const line = this.codeLines[this.currentLine];
        const lineElement = document.getElementById(`line-${this.currentLine}`);
        
        if (!lineElement) return;

        lineElement.classList.add('typing');
        
        if (this.currentChar >= line.length) {
            // Line complete, move to next line
            lineElement.classList.remove('typing');
            lineElement.classList.add('complete');
            this.currentLine++;
            this.currentChar = 0;
            this.updateCursorPosition();
            
            setTimeout(() => {
                this.startTyping();
            }, this.lineDelay);
            return;
        }

        // Type next character
        const char = line[this.currentChar];
        const charSpan = document.createElement('span');
        charSpan.className = 'code-char typed';
        
        // Handle spaces and preserve indentation
        if (char === ' ') {
            charSpan.innerHTML = '&nbsp;';
        } else {
            charSpan.textContent = char;
        }
        
        // Apply syntax highlighting
        this.applySyntaxHighlighting(charSpan, line, this.currentChar);
        
        lineElement.appendChild(charSpan);
        
        this.currentChar++;
        this.updateCursorPosition();
        
        setTimeout(() => {
            this.typeCurrentLine();
        }, this.typingSpeed);
    }

    applySyntaxHighlighting(element, line, charIndex) {
        const fullText = line.substring(0, charIndex + 1);
        
        // PHP tags
        if (fullText.includes('<?php') || fullText.includes('?>')) {
            element.className += ' php-tag';
        }
        // Comments
        else if (line.trim().startsWith('//')) {
            element.className += ' comment';
        }
        // Keywords
        else if (this.isKeyword(element.textContent, line, charIndex)) {
            element.className += ' keyword';
        }
        // Class names
        else if (this.isClassName(element.textContent, line, charIndex)) {
            element.className += ' class-name';
        }
        // Variables
        else if (element.textContent === '$' || (charIndex > 0 && line[charIndex - 1] === '$')) {
            element.className += ' variable';
        }
        // Function names
        else if (this.isFunctionName(element.textContent, line, charIndex)) {
            element.className += ' function-name';
        }
        // Strings
        else if (this.isInString(line, charIndex)) {
            element.className += ' string';
        }
    }

    isKeyword(char, line, index) {
        const keywords = ['class', 'private', 'public', 'function', 'return', 'new'];
        const wordStart = this.getWordStart(line, index);
        const word = line.substring(wordStart, index + 1);
        return keywords.some(keyword => keyword.startsWith(word) && word.length > 0);
    }

    isClassName(char, line, index) {
        const classNames = ['LearningPlatform', 'Student'];
        const wordStart = this.getWordStart(line, index);
        const word = line.substring(wordStart, index + 1);
        return classNames.some(className => className.startsWith(word) && word.length > 0);
    }

    isFunctionName(char, line, index) {
        const functions = ['enrollStudent', 'assignCourse', 'getProgress', 'calculateProgress'];
        const wordStart = this.getWordStart(line, index);
        const word = line.substring(wordStart, index + 1);
        return functions.some(func => func.startsWith(word) && word.length > 0);
    }

    isInString(line, index) {
        let inString = false;
        let stringChar = null;
        
        for (let i = 0; i <= index; i++) {
            if ((line[i] === '"' || line[i] === "'") && (i === 0 || line[i - 1] !== '\\')) {
                if (!inString) {
                    inString = true;
                    stringChar = line[i];
                } else if (line[i] === stringChar) {
                    inString = false;
                    stringChar = null;
                }
            }
        }
        
        return inString;
    }

    getWordStart(line, index) {
        let start = index;
        while (start > 0 && /[a-zA-Z0-9_$]/.test(line[start - 1])) {
            start--;
        }
        return start;
    }

    updateCursorPosition() {
        if (!this.cursor) return;
        
        const lineElement = document.getElementById(`line-${this.currentLine}`);
        if (!lineElement) return;
        
        const rect = lineElement.getBoundingClientRect();
        const contentRect = document.querySelector('.code-content').getBoundingClientRect();
        
        this.cursor.style.left = (rect.left - contentRect.left + lineElement.offsetWidth) + 'px';
        this.cursor.style.top = (rect.top - contentRect.top) + 'px';
    }

    resetAnimation() {
        this.currentLine = 0;
        this.currentChar = 0;
        
        // Clear all lines
        const codeLines = document.querySelectorAll('.code-line');
        codeLines.forEach(line => {
            line.innerHTML = '';
            line.classList.remove('typing');
        });
        
        // Reset cursor position
        if (this.cursor) {
            this.cursor.style.left = '30px';
            this.cursor.style.top = '30px';
        }
    }

    setupFloatingElements() {
        const codingSimulator = document.querySelector('.coding-simulator');
        if (!codingSimulator) return;
        
        const floatingElements = [
            { text: '<?php', top: '20%', right: '10%', delay: 0 },
            { text: 'function()', bottom: '30%', left: '15%', delay: 2000 },
            { text: 'class', top: '60%', right: '20%', delay: 4000 },
            { text: '$variable', bottom: '20%', right: '30%', delay: 1000 }
        ];

        floatingElements.forEach((element, index) => {
            const div = document.createElement('div');
            div.className = 'floating-code';
            div.textContent = element.text;
            div.style.position = 'absolute';
            div.style.animation = `floatCode ${8 + index * 2}s ease-in-out infinite`;
            div.style.animationDelay = `${element.delay}ms`;
            
            // Set position
            if (element.top) div.style.top = element.top;
            if (element.bottom) div.style.bottom = element.bottom;
            if (element.left) div.style.left = element.left;
            if (element.right) div.style.right = element.right;
            
            // Set colors based on type
            if (element.text.includes('<?php')) {
                div.style.color = 'rgba(86, 156, 214, 0.4)';
                div.style.fontSize = '28px';
            } else if (element.text.includes('function')) {
                div.style.color = 'rgba(206, 145, 120, 0.4)';
                div.style.fontSize = '22px';
            } else if (element.text.includes('class')) {
                div.style.color = 'rgba(76, 201, 176, 0.4)';
                div.style.fontSize = '24px';
            } else if (element.text.includes('$')) {
                div.style.color = 'rgba(156, 220, 254, 0.4)';
                div.style.fontSize = '20px';
            }
            
            codingSimulator.appendChild(div);
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for other animations to start
    setTimeout(() => {
        new PHPCodingAnimation();
    }, 1000);
});

// Handle window resize
window.addEventListener('resize', function() {
    const animation = window.phpCodingAnimation;
    if (animation && animation.cursor) {
        animation.updateCursorPosition();
    }
});
