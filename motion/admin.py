from django.contrib import admin
from .models import Weight, BloodPressure


@admin.register(Weight)
class WeightAdmin(admin.ModelAdmin):
    list_display = ('date', 'weight', 'fat', 'muscle', 'bmi', 'age')
    list_filter = ('date', 'gender')
    search_fields = ('date',)
    ordering = ('-date',)
    readonly_fields = ('bmi',)
    fieldsets = (
        ('General Information', {
            'fields': ('person_id', 'date', 'date_time', 'gender', 'age', 'height')
        }),
        ('Body Composition', {
            'fields': ('weight', 'fat', 'muscle', 'bone', 'vfat', 'moisture', 'bmi', 'calorie')
        }),
        ('Calculated Fields', {
            'fields': ('muscle_p',),
            'classes': ('collapse',)
        }),
    )


@admin.register(BloodPressure)
class BloodPressureAdmin(admin.ModelAdmin):
    list_display = ('date', 'systolic', 'diastolic', 'get_bp_status')
    list_filter = ('date',)
    search_fields = ('date',)
    ordering = ('-date',)
    
    def get_bp_status(self, obj):
        """Display blood pressure status"""
        if obj.systolic < 120 and obj.diastolic < 80:
            return "Normal"
        elif obj.systolic < 130 and obj.diastolic < 80:
            return "Elevated"
        elif obj.systolic < 140 or obj.diastolic < 90:
            return "Stage 1 Hypertension"
        else:
            return "Stage 2 Hypertension"
    get_bp_status.short_description = "Status"
