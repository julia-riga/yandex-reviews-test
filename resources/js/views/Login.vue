<template>
    <div class="login-container">
        <h2>Вход</h2>
        <form @submit.prevent="login">
            <div class="form-group">
                <label>Email</label>
                <input v-model="email" type="email" required />
            </div>
            <div class="form-group">
                <label>Пароль</label>
                <input v-model="password" type="password" required />
            </div>
            <div v-if="error" class="error">{{ error }}</div>
            <button type="submit" :disabled="loading">Войти</button>
        </form>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const email = ref("admin@example.com");
const password = ref("password");
const error = ref("");
const loading = ref(false);

const login = async () => {
    loading.value = true;
    error.value = "";
    try {
        const response = await axios.post("/api/login", {
            email: email.value,
            password: password.value,
        });
        localStorage.setItem("token", response.data.token);
        router.push("/companies");
    } catch (err) {
        error.value = "Неверные данные";
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.login-container {
    max-width: 400px;
    margin: 50px auto;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.form-group {
    margin-bottom: 15px;
}
label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
button {
    width: 100%;
    padding: 12px;
    background: #4caf50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
button:disabled {
    background: #ccc;
}
.error {
    color: #f44336;
    margin-bottom: 10px;
}
</style>
