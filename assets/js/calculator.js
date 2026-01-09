// assets/js/calculator.js
// Calculator untuk kasir (pembayaran)

class Calculator {
    constructor() {
        this.currentValue = '0';
        this.previousValue = '';
        this.operation = null;
        this.display = document.getElementById('calculatorDisplay');
    }
    
    clear() {
        this.currentValue = '0';
        this.previousValue = '';
        this.operation = null;
        this.updateDisplay();
    }
    
    delete() {
        if (this.currentValue.length > 1) {
            this.currentValue = this.currentValue.slice(0, -1);
        } else {
            this.currentValue = '0';
        }
        this.updateDisplay();
    }
    
    appendNumber(number) {
        if (this.currentValue === '0' && number !== '.') {
            this.currentValue = number.toString();
        } else if (number === '.' && this.currentValue.includes('.')) {
            return;
        } else {
            this.currentValue += number.toString();
        }
        this.updateDisplay();
    }
    
    chooseOperation(operation) {
        if (this.currentValue === '') return;
        
        if (this.previousValue !== '') {
            this.calculate();
        }
        
        this.operation = operation;
        this.previousValue = this.currentValue;
        this.currentValue = '';
    }
    
    calculate() {
        let result;
        const prev = parseFloat(this.previousValue);
        const current = parseFloat(this.currentValue);
        
        if (isNaN(prev) || isNaN(current)) return;
        
        switch (this.operation) {
            case '+':
                result = prev + current;
                break;
            case '-':
                result = prev - current;
                break;
            case '*':
                result = prev * current;
                break;
            case '/':
                result = prev / current;
                break;
            default:
                return;
        }
        
        this.currentValue = result.toString();
        this.operation = null;
        this.previousValue = '';
        this.updateDisplay();
    }
    
    updateDisplay() {
        if (this.display) {
            // Format as Rupiah
            const value = parseFloat(this.currentValue);
            if (!isNaN(value)) {
                this.display.textContent = 'Rp ' + value.toLocaleString('id-ID');
            } else {
                this.display.textContent = 'Rp 0';
            }
        }
    }
    
    getValue() {
        return parseFloat(this.currentValue) || 0;
    }
    
    setValue(value) {
        this.currentValue = value.toString();
        this.updateDisplay();
    }
}

// Initialize calculator when document is ready
let calculator;

document.addEventListener('DOMContentLoaded', function() {
    calculator = new Calculator();
    
    // Number buttons
    document.querySelectorAll('.calc-number').forEach(button => {
        button.addEventListener('click', function() {
            calculator.appendNumber(this.getAttribute('data-value'));
        });
    });
    
    // Operation buttons
    document.querySelectorAll('.calc-operation').forEach(button => {
        button.addEventListener('click', function() {
            calculator.chooseOperation(this.getAttribute('data-operation'));
        });
    });
    
    // Clear button
    const clearBtn = document.getElementById('calcClear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            calculator.clear();
        });
    }
    
    // Delete button
    const deleteBtn = document.getElementById('calcDelete');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            calculator.delete();
        });
    }
    
    // Equals button
    const equalsBtn = document.getElementById('calcEquals');
    if (equalsBtn) {
        equalsBtn.addEventListener('click', function() {
            calculator.calculate();
        });
    }
    
    // Quick amount buttons (untuk kasir)
    document.querySelectorAll('.quick-amount').forEach(button => {
        button.addEventListener('click', function() {
            const amount = parseFloat(this.getAttribute('data-amount'));
            calculator.setValue(amount);
        });
    });
    
    // Apply payment button
    const applyPaymentBtn = document.getElementById('applyPayment');
    if (applyPaymentBtn) {
        applyPaymentBtn.addEventListener('click', function() {
            const paymentAmount = calculator.getValue();
            const totalAmount = parseFloat(document.getElementById('totalAmount').value) || 0;
            
            if (paymentAmount < totalAmount) {
                alert('Jumlah pembayaran kurang dari total tagihan!');
                return;
            }
            
            // Set payment amount
            document.getElementById('paymentAmount').value = paymentAmount;
            
            // Calculate change
            const change = paymentAmount - totalAmount;
            document.getElementById('changeAmount').value = change;
            
            // Display change
            const changeDisplay = document.getElementById('changeDisplay');
            if (changeDisplay) {
                changeDisplay.textContent = 'Rp ' + change.toLocaleString('id-ID');
            }
            
            // Enable submit button
            const submitBtn = document.getElementById('submitPayment');
            if (submitBtn) {
                submitBtn.disabled = false;
            }
        });
    }
    
    // Keyboard support
    document.addEventListener('keydown', function(e) {
        if (!calculator) return;
        
        // Numbers
        if (e.key >= '0' && e.key <= '9') {
            calculator.appendNumber(e.key);
        }
        
        // Operations
        if (e.key === '+' || e.key === '-' || e.key === '*' || e.key === '/') {
            calculator.chooseOperation(e.key);
        }
        
        // Enter or Equals
        if (e.key === 'Enter' || e.key === '=') {
            e.preventDefault();
            calculator.calculate();
        }
        
        // Backspace
        if (e.key === 'Backspace') {
            e.preventDefault();
            calculator.delete();
        }
        
        // Escape
        if (e.key === 'Escape') {
            calculator.clear();
        }
        
        // Decimal point
        if (e.key === '.' || e.key === ',') {
            calculator.appendNumber('.');
        }
    });
});

// Payment calculation helper
function calculatePayment(totalAmount, paymentAmount) {
    const total = parseFloat(totalAmount) || 0;
    const payment = parseFloat(paymentAmount) || 0;
    
    if (payment < total) {
        return {
            valid: false,
            message: 'Pembayaran kurang dari total tagihan',
            change: 0
        };
    }
    
    return {
        valid: true,
        message: 'Pembayaran valid',
        change: payment - total
    };
}

// Quick amount setter
function setQuickAmount(amount) {
    if (calculator) {
        calculator.setValue(amount);
    }
}

// Format currency
function formatCurrency(amount) {
    return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
}
