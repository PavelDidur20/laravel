<!DOCTYPE html>
<html>
<head>
    <title>Todo List</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; }
        .task { padding: 10px; border: 1px solid #ddd; margin: 5px 0; }
        .status-select { margin-left: 20px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Todo List</h1>
    
 
    <div>
        <input type="text" id="title" placeholder="Название задачи">
        <select id="status">
            <option value="pending">Не начата</option>
            <option value="in_progress">В работе</option>
            <option value="completed">Завершена</option>
        </select>
        <button onclick="createTask()">Добавить</button>
        <div id="error" class="error"></div>
    </div>

 
    <div id="tasks"></div>

    <script>
      
        document.addEventListener('DOMContentLoaded', loadTasks);

        
        async function loadTasks() {
            const response = await fetch('/api/tasks');
            const tasks = await response.json();
            renderTasks(tasks);
        }

       
        async function createTask() {
            const task = {
                title: document.getElementById('title').value,
                status: document.getElementById('status').value
            };

            try {
                const response = await fetch('/api/tasks', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(task)
                });
                
                if (!response.ok) throw await response.json();
                
                document.getElementById('title').value = '';
                loadTasks();
            } catch (error) {
                showError(error);
            }
        }

    
        async function updateStatus(taskId, select) {
            try {
        const response = await fetch(`/api/tasks/${taskId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: select.value })
        });
        
        if (!response.ok) throw await response.json();
        
        // Обновляем список после изменения
        loadTasks();
    } catch (error) {
        showError(error);
    }
        }

    
        async function deleteTask(taskId) {
            if (!confirm('Удалить задачу?')) return;
            
            try {
                await fetch(`/api/tasks/${taskId}`, { method: 'DELETE' });
                loadTasks();
            } catch (error) {
                showError(error);
            }
        }

        function renderTasks(tasks) {
            const container = document.getElementById('tasks');
            container.innerHTML = tasks.map(task => `
                <div class="task">
                    ${task.title}
                    <select class="status-select" 
                            onchange="updateStatus(${task.id}, this)"
                            ${task.status === 'completed' ? 'disabled' : ''}>
                        <option ${task.status === 'pending' ? 'selected' : ''}>pending</option>
                        <option ${task.status === 'in_progress' ? 'selected' : ''}>in_progress</option>
                        <option ${task.status === 'completed' ? 'selected' : ''}>completed</option>
                    </select>
                    <button onclick="deleteTask(${task.id})">Удалить</button>
                </div>
            `).join('');
        }

        function showError(error) {
            const errorDiv = document.getElementById('error');
            errorDiv.innerHTML = error.message || 'Ошибка сервера';
            setTimeout(() => errorDiv.innerHTML = '', 3000);
        }
    </script>
</body>
</html>