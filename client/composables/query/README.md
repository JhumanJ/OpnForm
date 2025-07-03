# TanStack Query Composables Usage Guide

This directory contains TanStack Query composables that replace the previous Pinia-based data stores for server state management. Each composable follows a consistent pattern and provides queries, mutations, and utility functions.

## Available Composables

- `useWorkspaces()` - Workspace management
- `useForms()` - Form operations and submissions
- `useAuth()` - Authentication operations
- `useOAuth()` - OAuth provider management
- `useTemplates()` - Template operations
- `useTokens()` - Personal access token management

## Basic Usage Patterns

### Simple Data Fetching

```vue
<script setup>
const { list } = useWorkspaces();
const { data: workspaces, isLoading, error } = list();
</script>

<template>
    <div v-if="isLoading">Loading...</div>
    <div v-else-if="error">Error: {{ error.message }}</div>
    <div v-else>
        <div v-for="workspace in workspaces" :key="workspace.id">
            {{ workspace.name }}
        </div>
    </div>
</template>
```

### Paginated Data

```vue
<script setup>
const { paginatedList } = useWorkspaces();
const page = ref(1);
const filters = ref({});

const {
    data: workspaces,
    isLoading,
    isPreviousData,
    isFetching,
} = paginatedList(page, filters);

const nextPage = () => {
    if (!isPreviousData.value) {
        page.value++;
    }
};

const prevPage = () => {
    page.value = Math.max(page.value - 1, 1);
};
</script>

<template>
    <div>
        <div v-if="isLoading">Loading...</div>
        <div v-else-if="workspaces?.data">
            <div v-for="workspace in workspaces.data" :key="workspace.id">
                {{ workspace.name }}
            </div>

            <button @click="prevPage" :disabled="page === 1">Previous</button>
            <button @click="nextPage" :disabled="isPreviousData">
                Next {{ isFetching ? "(Loading...)" : "" }}
            </button>
        </div>
    </div>
</template>
```

### Mutations with Optimistic Updates

```vue
<script setup>
const { list, create, update, remove } = useWorkspaces();

const { data: workspaces } = list();
const createWorkspace = create({
    onSuccess: () => {
        useAlert().success("Workspace created!");
        form.reset();
    },
    onError: (error) => {
        useAlert().error("Failed to create workspace");
    },
});

const updateWorkspace = update({
    onSuccess: () => {
        useAlert().success("Workspace updated!");
    },
});

const deleteWorkspace = remove({
    onSuccess: () => {
        useAlert().success("Workspace deleted!");
    },
});

const form = useForm({
    name: "",
    icon: "",
});

const handleCreate = () => {
    createWorkspace.mutate(form.data());
};

const handleUpdate = (workspace) => {
    updateWorkspace.mutate({
        id: workspace.id,
        data: { name: "New Name" },
    });
};

const handleDelete = (workspaceId) => {
    if (confirm("Are you sure?")) {
        deleteWorkspace.mutate(workspaceId);
    }
};
</script>
```

### Cache-First Individual Items

```vue
<script setup>
const route = useRoute();
const { detail, update } = useWorkspaces();

// Will use cached data from list if available
const { data: workspace, isLoading } = detail(route.params.id);
const updateWorkspace = update();

const handleSave = (formData) => {
    updateWorkspace.mutate({
        id: route.params.id,
        data: formData,
    });
};
</script>
```

## Composable-Specific Examples

### useAuth() - Authentication

```vue
<script setup>
const { user, updateCredentials, logout } = useAuth()

// Get current user (cached from middleware)
const { data: currentUser, isLoading } = user()

// Update user credentials
const updateCreds = updateCredentials({
  onSuccess: () => {
    useAlert().success('Credentials updated!')
  }
})

// Logout user
const logoutUser = logout({
  onSuccess: () => {
    await navigateTo('/login')
  }
})

const handleUpdateCredentials = (formData) => {
  updateCreds.mutate(formData)
}

const handleLogout = () => {
  logoutUser.mutate()
}
</script>
```

### useForms() - Forms Management

```vue
<script setup>
const route = useRoute();
const { list, detail, create, submissions } = useForms();

// Get workspace forms
const workspaceId = route.params.workspaceId;
const { data: forms } = list(workspaceId);

// Get individual form
const formSlug = route.params.slug;
const { data: form } = detail(formSlug);

// Get form submissions
const { data: formSubmissions } = submissions(form.value?.id);

// Create new form
const createForm = create({
    onSuccess: (newForm) => {
        navigateTo(`/forms/${newForm.slug}/edit`);
    },
});

const handleCreateForm = (formData) => {
    createForm.mutate({
        ...formData,
        workspace_id: workspaceId,
    });
};
</script>
```

### useTemplates() - Templates

```vue
<script setup>
const { list, detail, create } = useTemplates();

// Get all templates
const { data: templates, isLoading } = list();

// Get template by slug
const templateSlug = "contact-form";
const { data: template } = detail(templateSlug);

// Create form from template
const createFromTemplate = create({
    onSuccess: (newForm) => {
        useAlert().success("Form created from template!");
        navigateTo(`/forms/${newForm.slug}`);
    },
});

const useTemplate = (template) => {
    createFromTemplate.mutate({
        name: `${template.name} Copy`,
        properties: template.properties,
        workspace_id: currentWorkspace.value.id,
    });
};
</script>
```

### useOAuth() - OAuth Providers

```vue
<script setup>
const { providers, connect, remove } = useOAuth();

// Get OAuth providers
const { data: oauthProviders } = providers();

// Connect OAuth provider
const connectProvider = connect({
    onSuccess: () => {
        useAlert().success("Provider connected!");
    },
});

// Remove OAuth provider
const removeProvider = remove({
    onSuccess: () => {
        useAlert().success("Provider disconnected!");
    },
});

const handleConnect = (service, authData) => {
    connectProvider.mutate({ service, data: authData });
};

const handleDisconnect = (providerId) => {
    removeProvider.mutate(providerId);
};
</script>
```

### useTokens() - API Tokens

```vue
<script setup>
const { list, create, remove } = useTokens();

// Get API tokens
const { data: tokens } = list();

// Create new token
const createToken = create({
    onSuccess: (newToken) => {
        useAlert().success("Token created!");
        // Show token value to user (only available on creation)
        tokenValue.value = newToken.token;
    },
});

// Delete token
const deleteToken = remove({
    onSuccess: () => {
        useAlert().success("Token deleted!");
    },
});

const tokenValue = ref("");
const form = reactive({
    name: "",
    abilities: ["read"],
});

const handleCreateToken = () => {
    createToken.mutate(form);
};

const handleDeleteToken = (tokenId) => {
    if (confirm("Are you sure?")) {
        deleteToken.mutate(tokenId);
    }
};
</script>
```

## Advanced Patterns

### Dependent Queries

```vue
<script setup>
const { detail: workspaceDetail } = useWorkspaces();
const { list: formsList } = useForms();

const workspaceId = ref("123");

// Get workspace first
const { data: workspace } = workspaceDetail(workspaceId);

// Then get forms for that workspace (dependent query)
const { data: forms } = formsList(workspaceId, {
    enabled: !!workspace.value, // Only run when workspace is loaded
});
</script>
```

### Manual Cache Updates vs Invalidation

```vue
<script setup>
// ✅ Preferred: Manual cache updates (no extra network requests)
const updateForm = useForms().update({
    onSuccess: (updatedForm, { id }) => {
        // Cache is automatically updated by the composable
        useAlert().success("Form updated!");
    },
});

// ❌ Avoid unless necessary: Invalidation (triggers refetch)
const updateFormWithInvalidation = useMutation({
    mutationFn: ({ id, data }) => formsApi.update(id, data),
    onSuccess: () => {
        queryClient.invalidateQueries(["forms"]); // Will refetch all form queries
    },
});
</script>
```

### Background Refetching

```vue
<script setup>
// Data will refetch in background when stale
const { data: forms, isFetching } = useForms().list(workspaceId, {
    staleTime: 2 * 60 * 1000, // Consider stale after 2 minutes
    refetchOnWindowFocus: true, // Refetch when window gains focus
});

// Show subtle loading indicator for background updates
const isBackgroundRefetching = computed(() => {
    return isFetching.value && !!forms.value;
});
</script>

<template>
    <div>
        <div v-if="isBackgroundRefetching" class="bg-blue-100 text-sm">
            Updating...
        </div>
        <!-- Your content -->
    </div>
</template>
```

### Error Handling

```vue
<script setup>
const { list, create } = useForms();

// Query with error handling
const {
    data: forms,
    error,
    isError,
} = list(workspaceId, {
    retry: (failureCount, error) => {
        // Don't retry on 4xx errors
        if (error?.status >= 400 && error?.status < 500) {
            return false;
        }
        return failureCount < 3;
    },
    onError: (error) => {
        useAlert().error(`Failed to load forms: ${error.message}`);
    },
});

// Mutation with error handling
const createForm = create({
    onError: (error, variables, context) => {
        if (error?.status === 422) {
            // Handle validation errors
            useAlert().error("Please check your form data");
        } else {
            useAlert().error("Failed to create form");
        }
    },
});
</script>
```

## Migration from Pinia Stores

### Before (Pinia Store)

```vue
<script setup>
const workspaceStore = useWorkspacesStore();

// Load data
await workspaceStore.loadAll();

// Access data
const workspaces = computed(() => workspaceStore.getAll);

// Create workspace
await workspaceStore.create(formData);
</script>
```

### After (TanStack Query)

```vue
<script setup>
const { list, create } = useWorkspaces();

// Load data (automatic caching and background updates)
const { data: workspaces, isLoading } = list();

// Create workspace
const createWorkspace = create({
    onSuccess: () => {
        // Cache automatically updated
        useAlert().success("Created!");
    },
});

const handleCreate = (formData) => {
    createWorkspace.mutate(formData);
};
</script>
```

## Benefits Achieved

✅ **Automatic caching** - No manual cache management needed  
✅ **Background refetching** - Data stays fresh automatically  
✅ **Optimistic updates** - UI responds immediately to mutations  
✅ **Smart invalidation** - Manual cache updates prevent unnecessary requests  
✅ **SSR support** - Works seamlessly with Nuxt hydration  
✅ **DevTools** - Excellent debugging experience with Vue Query DevTools  
✅ **Error handling** - Built-in retry logic and error boundaries  
✅ **Loading states** - Comprehensive loading and fetching states  
✅ **Type safety** - Better TypeScript support than Pinia stores

## DevTools

Add the Vue Query DevTools for debugging:

```vue
<!-- In your app.vue or layout -->
<template>
    <div>
        <!-- Your app content -->
        <VueQueryDevtools v-if="$config.public.dev" />
    </div>
</template>

<script setup>
import { VueQueryDevtools } from "@tanstack/vue-query-devtools";
</script>
```

Access the devtools by pressing the Vue Query button in the bottom corner of your app during development.
