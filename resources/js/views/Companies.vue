<template>
    <div class="companies-container">
        <div class="header">
            <h1>Настройки</h1>
            <button @click="logout" class="btn-logout">Выйти</button>
        </div>

        <div class="form-section">
            <h2>Добавить организацию</h2>
            <form @submit.prevent="addCompany">
                <div class="form-group">
                    <label>Ссылка на Яндекс.Карты:</label>
                    <input
                        type="url"
                        v-model="companyUrl"
                        placeholder="https://yandex.ru/maps/org/..."
                        required
                    />
                </div>
                <div v-if="error" class="error">{{ error }}</div>
                <button type="submit" :disabled="loading" class="btn-primary">
                    {{
                        loading ? "Загрузка..." : "Сохранить и загрузить отзывы"
                    }}
                </button>
            </form>
        </div>

        <div v-if="companies && companies.length > 0" class="companies-list">
            <h2>Мои организации</h2>
            <div
                v-for="company in companies"
                :key="company.id"
                class="company-card"
            >
                <h3>{{ company.name || "Без названия" }}</h3>
                <p class="url">{{ company.url }}</p>
                <div v-if="company.rating" class="rating">
                    ⭐ {{ company.rating }} ({{ company.review_count }} отзывов)
                </div>
                <router-link :to="`/companies/${company.id}`" class="btn-view">
                    Смотреть отзывы
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const companyUrl = ref("");
const companies = ref([]);
const error = ref("");
const loading = ref(false);

const api = axios.create({
    baseURL: "/api",
    headers: { "Content-Type": "application/json" },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem("token");
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});

onMounted(async () => {
    await loadCompanies();
});

const loadCompanies = async () => {
    try {
        const response = await api.get("/companies");
        companies.value = Array.isArray(response.data)
            ? response.data
            : response.data.data || [];
    } catch (err) {
        console.error("Ошибка загрузки компаний:", err);
        companies.value = [];
    }
};

const addCompany = async () => {
    loading.value = true;
    error.value = "";
    try {
        await api.post("/companies", { url: companyUrl.value });
        companyUrl.value = "";
        await loadCompanies();
        alert("Организация успешно добавлена!");
    } catch (err) {
        const errors = err.response?.data?.errors;
        if (errors && errors.url) {
            error.value = errors.url[0];
        } else {
            error.value = err.response?.data?.error || "Ошибка при добавлении";
        }
    } finally {
        loading.value = false;
    }
};

const logout = async () => {
    try {
        await api.post("/logout");
    } catch (err) {}
    localStorage.removeItem("token");
    router.push("/");
};
</script>

<style scoped>
.companies-container {
    max-width: 800px;
    margin: 0 auto;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.btn-logout {
    padding: 8px 16px;
    background: #f44336;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.form-section {
    background: white;
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.form-group {
    margin-bottom: 20px;
}
label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}
input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}
.btn-primary {
    width: 100%;
    padding: 12px;
    background: #4caf50;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
}
.btn-primary:disabled {
    background: #ccc;
    cursor: not-allowed;
}
.error {
    color: #f44336;
    margin-bottom: 15px;
    padding: 10px;
    background: #ffebee;
    border-radius: 4px;
}
.companies-list {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.company-card {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 15px;
}
.company-card h3 {
    margin: 0 0 10px 0;
}
.url {
    color: #666;
    font-size: 14px;
    margin-bottom: 10px;
}
.rating {
    margin-bottom: 15px;
    font-weight: bold;
}
.btn-view {
    display: inline-block;
    padding: 8px 16px;
    background: #2196f3;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}
</style>
